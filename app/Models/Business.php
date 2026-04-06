<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ApprovalStatus;
use App\Enums\BusinessLifecycleStatus;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasAuditLogs;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon; // ✅ INSERTADO: para manejo de fechas

class Business extends Model
{
    use HasFactory, SoftDeletes, HasAuditLogs;

    /* ---------------------------------------------------
     |  Tabla y PK
     ---------------------------------------------------*/
    protected $table      = 'businesses';
    protected $primaryKey = 'business_code';
    public    $incrementing = false;
    protected $keyType      = 'string';

    protected $fillable = [
        'business_code', 'index', 'description',
        'reinsurance_type', 'risk_covered', 'business_type',
        'premium_type', 'purpose', 'claims_type',
        'reinsurer_id', 'parent_id', 'renewed_from_id',
        'producer_id', 'currency_id', 'region_id',
        'approval_status', 'approval_status_updated_at',
        'business_lifecycle_status', 'business_lifecycle_status_updated_at',
        'created_by_user', 'source_code'
    ];

    protected $casts = [
        'approval_status'            => ApprovalStatus::class,
        'business_lifecycle_status'  => BusinessLifecycleStatus::class,
        'approval_status_updated_at' => 'datetime',
        'created_at'                 => 'datetime',
        'updated_at'                 => 'datetime',
    ];

    /* ---------------------------------------------------
     |  ➜  Relaciones belongsTo
     ---------------------------------------------------*/
    public function reinsurer()
    {
        return $this->belongsTo(Reinsurer::class);
    }

    public function producer()
    {
        return $this->belongsTo(Partner::class, 'producer_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by_user');
    }

    /* ---------------------------------------------------
    |  ➜  Relaciones self-referenciales
    ---------------------------------------------------*/
    public function renewedFrom()
    {
        return $this->belongsTo(self::class, 'renewed_from_id');
    }

    /* ---------------------------------------------------
     |  ➜  Relaciones hasMany / hasOne
     ---------------------------------------------------*/
    public function renewals()
    {
        return $this->hasMany(self::class, 'renewed_from_id', 'business_code');
    }

    public function liabilityStructures()
    {
        return $this->hasMany(LiabilityStructure::class, 'business_code', 'business_code');
    }

    public function operativeDocs()
    {
        return $this->hasMany(OperativeDoc::class, 'business_code', 'business_code');
    }

    public function coverages()
    {
        return $this->belongsToMany(
            Coverage::class,
            'liability_structures',
            'business_code',
            'coverage_id',
            'business_code',
            'id'
        )->distinct();
    }

    protected function getAuditLabelIdentifier(): ?string
    {
        return $this->business_code ?: null;
    }

    public function treaty()
    {
        return $this->belongsTo(Treaty::class, 'parent_id', 'treaty_code');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Treaty::class, 'parent_id', 'treaty_code');
    }

    /* =========================================================
     |  ✅ INSERTADO: Resolver lifecycle status del negocio
     |  Reglas actuales:
     |  1) Sin documentos => On Hold
     |  2) Si al menos un doc está vigente:
     |        - si alguno vence en <= 30 días => To Expire
     |        - si no => In Force
     |  3) Si no hay docs vigentes:
     |        - si el documento más reciente es cancelación (type 5) => Cancelled
     |        - si no => Expired
     ========================================================= */
    public function resolveLifecycleStatus(?Carbon $today = null): BusinessLifecycleStatus
    {
        $today = ($today ?? now())->copy()->startOfDay();

        $docs = $this->operativeDocs()
            ->withoutTrashed()
            ->get([
                'id',
                'index',
                'operative_doc_type_id',
                'inception_date',
                'expiration_date',
                'created_at',
            ]);

        // 1) Sin documentos
        if ($docs->isEmpty()) {
            return BusinessLifecycleStatus::ON_HOLD;
        }

        // 2) Último documento relevante del negocio
        $latestDoc = $docs
            ->sortByDesc(function ($doc) {
                return [
                    $doc->index ?? 0,
                    $doc->inception_date ? Carbon::parse($doc->inception_date)->timestamp : 0,
                    $doc->created_at ? Carbon::parse($doc->created_at)->timestamp : 0,
                ];
            })
            ->first();

        // 3) Si el último doc es cancelación y ya aplica, domina el lifecycle
        if (
            $latestDoc
            && (int) $latestDoc->operative_doc_type_id === 5
            && $latestDoc->inception_date
            && $today->gte(Carbon::parse($latestDoc->inception_date)->startOfDay())
        ) {
            return BusinessLifecycleStatus::CANCELLED;
        }

        // 4) Documentos vigentes (solo si no quedó cancelado)
        $activeDocs = $docs->filter(function ($doc) use ($today) {
            if (empty($doc->inception_date) || empty($doc->expiration_date)) {
                return false;
            }

            $start = Carbon::parse($doc->inception_date)->startOfDay();
            $end   = Carbon::parse($doc->expiration_date)->startOfDay();

            return $today->betweenIncluded($start, $end);
        });

        if ($activeDocs->isNotEmpty()) {
            $hasToExpire = $activeDocs->contains(function ($doc) use ($today) {
                $end = Carbon::parse($doc->expiration_date)->startOfDay();
                $daysLeft = $today->diffInDays($end, false);

                return $daysLeft >= 0 && $daysLeft <= 30;
            });

            return $hasToExpire
                ? BusinessLifecycleStatus::TO_EXPIRE
                : BusinessLifecycleStatus::IN_FORCE;
        }

        // 5) Si no hay vigentes y el último no fue cancelación
        return BusinessLifecycleStatus::EXPIRED;
    }






    /* =========================================================
     |  ✅ INSERTADO: Persistir lifecycle status solo si cambia
     ========================================================= */
    public function refreshLifecycleStatus(): void
    {
        $newStatus = $this->resolveLifecycleStatus();

        if ($this->business_lifecycle_status !== $newStatus) {
            $this->forceFill([
                'business_lifecycle_status' => $newStatus,
                'business_lifecycle_status_updated_at' => now(),
            ])->saveQuietly();
        }
    }

    protected static function booted(): void
    {
        /* =====================================================
         |  ✅ INSERTADO: al crear un Business nuevo,
         |  si no trae status, se asigna On Hold por defecto
         ===================================================== */
        static::creating(function (Business $business) {
            if (blank($business->business_lifecycle_status)) {
                $business->business_lifecycle_status = BusinessLifecycleStatus::ON_HOLD;
                $business->business_lifecycle_status_updated_at = now();
            }
        });

        // Borrado y Restauracion para Liability Structures
        //-------------------------------------------------
        static::deleting(function (Business $business) {
            if ($business->isForceDeleting()) {
                $business->liabilityStructures()
                    ->withTrashed()
                    ->forceDelete();
            } else {
                $business->liabilityStructures()
                    ->get()
                    ->each
                    ->delete();
            }
        });

        static::restoring(function (Business $business) {
            $business->liabilityStructures()
                ->onlyTrashed()
                ->restore();
        });

        // Borrado y Restauracion para Operative Documents
        //------------------------------------------------
        static::deleting(function (Business $business) {
            if ($business->isForceDeleting()) {
                $business->operativeDocs()->withTrashed()->each->forceDelete();
            } else {
                $business->operativeDocs()->get()->each->delete();
            }
        });

        static::restoring(function (Business $business) {
            $business->operativeDocs()->onlyTrashed()->restore();
        });
    }
}