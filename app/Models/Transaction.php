<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    //
    use SoftDeletes;

    /* ---------------------------------------------------
     |  Tabla y PK
     ---------------------------------------------------*/
    protected $table      = 'transactions';
    protected $primaryKey = 'id';
    public    $incrementing = false;          // PK no autoincremental
    protected $keyType      = 'string';       // PK tipo string


    protected $fillable = [
        'id',
        'index',
        'proportion',
        'exch_rate',
        'due_date',
        'remmitance_code',
        'op_document_id',
        'transaction_type_id',
        'transaction_status_id',
    ];
    
    
    
    public function operativeDoc()
    {
        return $this->belongsTo(OperativeDoc::class, 'op_document_id');
    }

    protected static function booted()
    {
        // Asigna UUID si no existe
        static::creating(function ($model) {
            if (! $model->getKey()) {
                $model->{$model->getKeyName()} = (string) \Illuminate\Support\Str::uuid();
            }

            // Asigna automáticamente el índice
            if (! $model->index) {
                $maxIndex = self::where('op_document_id', $model->op_document_id)
                    ->withoutTrashed()
                    ->max('index');
                $model->index = $maxIndex ? $maxIndex + 1 : 1;
            }

            // Asignaciones forzadas por defecto
            $model->transaction_type_id ??= 1;
            $model->transaction_status_id ??= 1;
            $model->remittance_code ??= null;
        });

        // Reordenamiento al eliminar
        static::deleted(function ($model) {
            self::where('op_document_id', $model->op_document_id)
                ->withoutTrashed()
                ->orderBy('index')
                ->get()
                ->values()
                ->each(function ($record, $key) {
                    $record->update(['index' => $key + 1]);
                });
        });
    }


}
