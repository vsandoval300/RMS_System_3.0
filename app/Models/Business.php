<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ApprovalStatus;           // 👈 tu Enum PHP 8.1+
use App\Enums\BusinessLifecycleStatus;  // 👈 tu Enum PHP 8.1+
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasAuditLogs;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Business extends Model
{
    use HasFactory, SoftDeletes, HasAuditLogs;

    /* ---------------------------------------------------
     |  Tabla y PK
     ---------------------------------------------------*/
    protected $table      = 'businesses';
    protected $primaryKey = 'business_code';
    public    $incrementing = false;          // PK no autoincremental
    protected $keyType      = 'string';       // PK tipo string

    protected $fillable = [
        'business_code', 'index', 'description',
        'reinsurance_type', 'risk_covered', 'business_type',
        'premium_type', 'purpose', 'claims_type',
        'reinsurer_id', 'parent_id', 'renewed_from_id',
        'producer_id', 'currency_id', 'region_id',
        'approval_status', 'approval_status_updated_at',
        'business_lifecycle_status', 'business_lifecycle_status_updated_at',
        'created_by_user','source_code'
    ];

    protected $casts = [
        'approval_status'           => ApprovalStatus::class,
        'business_lifecycle_status' => BusinessLifecycleStatus::class,
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

    public function producer()          // tabla partners (foreignId producer_id)
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
    /* public function parent()
    {
        return $this->belongsTo(self::class,'parent_id');  
    } */

    public function renewedFrom()
    {
        return $this->belongsTo(self::class,'renewed_from_id');
    }

    /* ---------------------------------------------------
     |  ➜  Relaciones hasMany / hasOne
     ---------------------------------------------------*/
   /*  public function children()   // inverso de parent()
    {
        return $this->hasMany(self::class,'parent_id','business_code');
    } */

    public function renewals()   // inverso de renewedFrom()
    {
        return $this->hasMany(self::class,'renewed_from_id','business_code');
    }

    public function liabilityStructures()
    {
        return $this->hasMany(LiabilityStructure::class,'business_code', 'business_code');
    }

    public function operativeDocs()
    {
        return $this->hasMany(OperativeDoc::class,'business_code','business_code');
    }

    public function coverages()
    {
        return $this->belongsToMany(Coverage::class, 'liability_structures',
            'business_code', 'coverage_id', 'business_code', 'id'
        )->distinct();
    }

    protected function getAuditLabelIdentifier(): ?string
    {
        // Para Business, el identificador que quieres es business_code
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

/* Claro. En tu modelo Business, la función booted():
    Se ejecuta cuando el modelo se “inicializa” y sirve para registrar eventos de Eloquent (ganchos lifecycle).
    Intercepta el borrado y la restauración de un Business para propagar esas acciones a sus hijos LiabilityStructure.

En concreto:
    1. static::deleting(...)
        Soft delete ($business->delete()): marca con deleted_at todos los liability_structures
         relacionados (each->delete()), imitando una “cascada suave”.
        Force delete ($business->forceDelete() o si el registro ya está “trashed” y vuelves a borrar): elimina
         físicamente los liability_structures (withTrashed()->forceDelete()).

    2. static::restoring(...)
        Si restauras el Business ($business->restore()), restaura también todos los liability_structures que estaban
         en papelera (onlyTrashed()->restore()).

¿Por qué es necesario?
    Los FK con onDelete('cascade') solo actúan en hard deletes a nivel de BD.
    Con SoftDeletes, la BD no hace cascada; por eso registramos estos eventos para mantener la integridad lógica
     (padre e hijos comparten el mismo estado: activo/borrado/restaurado).

Requisitos/prácticas:
    El hijo (LiabilityStructure) debe usar use SoftDeletes;.
    Borrar/restaurar con Eloquent (no con Query Builder plano) para que sí se disparen los eventos. */

    protected static function booted(): void
    {
        // Borrado y Restauracion para Liability Structures
        //-------------------------------------------------
        // Al borrar un Business:
        static::deleting(function (Business $business) {
            if ($business->isForceDeleting()) {
                // Hard delete en cascada para los hijos
                $business->liabilityStructures()
                    ->withTrashed()
                    ->forceDelete();
            } else {
                // Soft delete de los hijos
                $business->liabilityStructures()
                    ->get()
                    ->each
                    ->delete();
            }
        });

        // Al restaurar un Business:
        static::restoring(function (Business $business) {
            $business->liabilityStructures()
                ->onlyTrashed()
                ->restore();
        });

        // Borrado y Restauracion para Operative Documents
        //------------------------------------------------
        static::deleting(function (Business $business) {
            if ($business->isForceDeleting()) {
                // Hard delete en cascada
                $business->operativeDocs()->withTrashed()->each->forceDelete();
            } else {
                // Soft delete de hijos
                $business->operativeDocs()->get()->each->delete();
            }
        });

        static::restoring(function (Business $business) {
            $business->operativeDocs()->onlyTrashed()->restore();
        });


    }
    
}




