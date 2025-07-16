<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OperativeDoc extends Model
{
    use HasFactory, SoftDeletes;

    /* --------------------------------------------------
     |  Tabla y asignación masiva
     --------------------------------------------------*/
    protected $table = 'operative_docs';

    /** 🟢 Declaración correcta para IDs tipo string */
    protected $primaryKey = 'id';
    public $incrementing = false;     // 👈 ID no es autoincremental
    protected $keyType = 'string';    // 👈 ID es string (varchar)

    protected $fillable = [
        'id',
        'operative_doc_type_id',
        'index',
        'description',
        'inception_date',
        'expiration_date',
        'document_path',
        'client_payment_tracking',
        'business_code',          // FK hacia businesses
    ];

    protected $casts = [
        'inception_date'  => 'date',
        'expiration_date' => 'date',
    ];

    /* --------------------------------------------------
     |  belongsTo
     --------------------------------------------------*/

    /** Negocio (Business) al que pertenece este documento */
    public function business()
    {
        return $this->belongsTo(
            Business::class,
            'business_code',      // FK en operative_docs
            'business_code'       // PK en businesses
        );
    }

    /** Tipo de documento operativo (business_doc_types) */
    public function docType()
    {
        return $this->belongsTo(
            BusinessDocType::class,
            'operative_doc_type_id'
        );
    }

    /* --------------------------------------------------
     |  hasMany
     --------------------------------------------------*/

    /** Esquemas asociados (businessdoc_schemes) */
    public function schemes()
    {
        return $this->hasMany(
            BusinessOpDocsScheme::class,
            'op_document_id'
        );
    }

    /** Insureds + coverages (businessdoc_insureds) */
    public function insureds()
    {
        return $this->hasMany(
            BusinessOpDocsInsured::class,
            'op_document_id'
        );
    }

    /** Transacciones (payments, etc.) */
    public function transactions()
    {
        return $this->hasMany(
            Transaction::class,
            'op_document_id'
        );
    }

    public function operativeDocs()
    {
        return $this->hasMany(
            OperativeDoc::class,
            'business_code',   // FK en operative_docs
            'business_code'    // PK en businesses
        );
    }




}

