<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BusinessOpDocsInsured extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'businessdoc_insureds';
    protected $primaryKey = 'id';
    public    $incrementing = false;          // PK no autoincremental
    protected $keyType      = 'string';       // PK tipo string

    protected $fillable = [
        'id',
        'op_document_id',   // FK → operative_docs.id
        'company_id',       // FK → companies.id
        'coverage_id',      // FK → coverages.id
        'premium',
    ];

    protected $casts = [
        'premium' => 'decimal:2',
    ];

    /* ─── belongsTo ─── */
    public function operativeDoc()
    {
        return $this->belongsTo(OperativeDoc::class, 'op_document_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function coverage()
    {
        return $this->belongsTo(Coverage::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'op_document_id', 'op_document_id');
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

}

