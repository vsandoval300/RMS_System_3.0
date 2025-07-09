<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ApprovalStatus;           // 👈 tu Enum PHP 8.1+
use App\Enums\BusinessLifecycleStatus;  // 👈 tu Enum PHP 8.1+

class Business extends Model
{
    use HasFactory;

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
    ];

    protected $casts = [
        'approval_status'           => ApprovalStatus::class,
        'business_lifecycle_status' => BusinessLifecycleStatus::class,
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

    /* ---------------------------------------------------
    |  ➜  Relaciones self-referenciales
    ---------------------------------------------------*/
    public function parent()
    {
        return $this->belongsTo(
            self::class,
            'parent_id',        // FK en esta tabla
            'business_code'     // PK en la tabla destino
        );
    }

    public function renewedFrom()
    {
        return $this->belongsTo(
            self::class,
            'renewed_from_id',
            'business_code'
        );
    }

    /* ---------------------------------------------------
     |  ➜  Relaciones hasMany / hasOne
     ---------------------------------------------------*/
    public function children()   // inverso de parent()
    {
        return $this->hasMany(
            self::class,
            'parent_id',
            'business_code'
        );
    }

    public function renewals()   // inverso de renewedFrom()
    {
        return $this->hasMany(
            self::class,
            'renewed_from_id',
            'business_code'
        );
    }

    public function liabilityStructures()
    {
        return $this->hasMany(
            LiabilityStructure::class,
            'business_code',    // FK en liability_structures
            'business_code'     // PK local
        );
    }

    public function operativeDocs()
    {
        return $this->hasMany(
            OperativeDoc::class,
            'business_code',
            'business_code'
        );
    }
}

