<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BusinessOpDocsScheme extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'businessdoc_schemes';
    protected $primaryKey = 'id';
    public    $incrementing = false;          // PK no autoincremental
    protected $keyType      = 'string';       // PK tipo string

    protected $fillable = [
        'id',
        'index',
        'op_document_id',   // FK → operative_docs.id
        'cscheme_id',       // FK → cost_schemes.id
    ];

    /* ─── belongsTo ─── */
    public function operativeDoc()
    {
        return $this->belongsTo(OperativeDoc::class, 'op_document_id');
    }

    public function costScheme()
    {
        return $this->belongsTo(CostScheme::class, 'cscheme_id');
    }

    
    protected static function booted()
    {
        static::creating(function ($model) {
            // UUID
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }

            // Asignar el siguiente índice disponible (solo considerando registros que no estén soft-deleted)
            if (empty($model->index) && $model->op_document_id) {
                $maxIndex = static::where('op_document_id', $model->op_document_id)
                    ->whereNull('deleted_at')
                    ->max('index');

                $model->index = $maxIndex ? $maxIndex + 1 : 1;
            }
        });

        static::deleted(function ($model) {
            // Reordenar los índices de los restantes
            $schemes = static::where('op_document_id', $model->op_document_id)
                ->whereNull('deleted_at')
                ->orderBy('index')
                ->get();

            $i = 1;
            foreach ($schemes as $scheme) {
                $scheme->index = $i++;
                $scheme->saveQuietly(); // evitar disparar eventos recursivos
            }
        });
    }



}





