<?php

namespace App\Models;

use Database\Seeders\BusinessDocInsuredsYelmoSeeder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use App\Models\Traits\HasAuditLogs;

class OperativeDoc extends Model
{
    use HasFactory, SoftDeletes, HasAuditLogs;

    /* --------------------------------------------------
     |  Tabla y asignación masiva
     --------------------------------------------------*/
    protected $table = 'operative_docs';

    /** 🟢 Declaración correcta para IDs tipo string */
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'operative_doc_type_id',
        'index',
        'description',
        'inception_date',
        'expiration_date',
        'af_mf',
        'roe_fs',
        'rep_date',
        'document_path',
        //'client_payment_tracking',
        'business_code',
        'created_by_user',
    ];

    protected $casts = [
        'inception_date'  => 'datetime',
        'expiration_date' => 'datetime',
        'rep_date'        => 'date',
    ];

    /* --------------------------------------------------
     |  belongsTo
     --------------------------------------------------*/

    /** Negocio (Business) al que pertenece este documento */
    public function business()
    {
        return $this->belongsTo(
            Business::class,
            'business_code',
            'business_code'
        );
    }

    /** Tipo de documento operativo (business_doc_types) */
    public function docType()
    {
        return $this->belongsTo(BusinessDocType::class, 'operative_doc_type_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by_user');
    }

    /* --------------------------------------------------
     |  hasMany
     --------------------------------------------------*/

    /** Esquemas asociados (businessdoc_schemes) */
    public function schemes()
    {
        return $this->hasMany(BusinessOpDocsScheme::class, 'op_document_id');
    }

    /** Insureds + coverages (businessdoc_insureds) */
    public function insureds()
    {
        return $this->hasMany(BusinessOpDocsInsured::class, 'op_document_id');
    }

    /** Transacciones (payments, etc.) */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'op_document_id', 'id');
    }

    public function operativeDocs()
    {
        return $this->hasMany(
            OperativeDoc::class,
            'business_code',
            'business_code'
        );
    }

    protected function getAuditOwnerModel(): Model
    {
        return $this->business ?? $this;
    }

    protected function getAuditLabelIdentifier(): ?string
    {
        $base = $this->business_code
            ?: $this->business?->business_code;

        if ($base && $this->index) {
            $suffix = str_pad($this->index, 2, '0', STR_PAD_LEFT);

            return "{$base}-{$suffix}";
        }

        $key = $this->getKey();

        return $key !== null ? (string) $key : null;
    }

    protected static function booted(): void
    {
        // Paso 1: Asignar automáticamente el índice al crear
        static::creating(function ($model) {
            $maxIndex = self::where('business_code', $model->business_code)
                ->withoutTrashed()
                ->max('index');

            $model->index = $maxIndex ? $maxIndex + 1 : 1;
        });

        /* =====================================================
         |  ✅ INSERTADO: al crear un documento, recalcular
         |  el lifecycle del negocio relacionado
         ===================================================== */
        static::created(function (OperativeDoc $model) {
            $model->business?->refreshLifecycleStatus();
        });

        /* =====================================================
         |  ✅ INSERTADO: al actualizar campos relevantes del doc,
         |  recalcular el lifecycle del negocio
         |
         |  Se consideran relevantes:
         |  - inception_date
         |  - expiration_date
         |  - operative_doc_type_id
         |  - business_code
         ===================================================== */
        static::updated(function (OperativeDoc $model) {
            if ($model->wasChanged([
                'inception_date',
                'expiration_date',
                'operative_doc_type_id',
                'business_code',
            ])) {
                // Si cambió de negocio, refresca tanto el actual como el original
                $originalBusinessCode = $model->getOriginal('business_code');

                if ($originalBusinessCode && $originalBusinessCode !== $model->business_code) {
                    $oldBusiness = Business::withTrashed()
                        ->where('business_code', $originalBusinessCode)
                        ->first();

                    $oldBusiness?->refreshLifecycleStatus();
                }

                $model->business?->refreshLifecycleStatus();
            }
        });

        // Paso 2: Reordenar índices al eliminar
        static::deleted(function ($model) {
            self::where('business_code', $model->business_code)
                ->withoutTrashed()
                ->orderBy('index')
                ->get()
                ->values()
                ->each(function ($record, $key) {
                    $record->update(['index' => $key + 1]);
                });

            /* =================================================
             |  ✅ INSERTADO: al eliminar un documento,
             |  recalcular el lifecycle del negocio
             ================================================= */
            $model->business?->refreshLifecycleStatus();

            // Paso 3: Eliminar archivo físico solo en forceDelete
            if ($model->isForceDeleting()
                && $model->document_path
                && Storage::disk('s3')->exists($model->document_path)) {
                Storage::disk('s3')->delete($model->document_path);
            }
        });

        /* =====================================================
         |  ✅ INSERTADO: al restaurar un documento,
         |  recalcular el lifecycle del negocio
         ===================================================== */
        static::restored(function (OperativeDoc $model) {
            $model->business?->refreshLifecycleStatus();
        });
    }
}