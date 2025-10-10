<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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
    
    protected $casts = [
        'due_date' => 'datetime',
        'proportion' => 'decimal:6',
        'exch_rate' => 'decimal:6',
    ];
    
    /* --------------------------------------------------
     |  belongsTo
     --------------------------------------------------*/
    public function type(): BelongsTo
    {
        // FK: transactions.transaction_type_id → transaction_types.id
        return $this->belongsTo(TransactionType::class, 'transaction_type_id');
    }

    public function status(): BelongsTo
    {
        // FK: transactions.transaction_status_id → transaction_statuses.id
        return $this->belongsTo(TransactionStatus::class, 'transaction_status_id');
    }

    // (Extra, por tu contexto previo)
    public function operativeDoc(): BelongsTo
    {
        return $this->belongsTo(OperativeDoc::class, 'op_document_id');
    }

    /* --------------------------------------------------
     |  hasMany
     --------------------------------------------------*/
    public function logs(): HasMany
    {
        // Enlaza por código: transaction_logs.transaction_code → transactions.remmitance_code
        return $this->hasMany(TransactionLog::class, 'transaction_id');
    }





    /* protected static function booted()
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
            $model->remmitance_code ??= null;
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
    } */
    

    protected static function booted()
    {
        static::creating(function ($model) {
            // 🛠️ Si viene sin PK → genera UUID (como ya tenías)
            if (! $model->getKey()) {
                $model->{$model->getKeyName()} = (string) \Illuminate\Support\Str::uuid();
            } else {
                // 🛠️ Si VIENE con PK y YA EXISTE en BD (incluyendo soft-deleted) → genera uno nuevo
                $exists = self::withTrashed()->whereKey($model->getKey())->exists();
                if ($exists) {
                    $model->{$model->getKeyName()} = (string) \Illuminate\Support\Str::uuid();
                }
            }

            // Índice auto si no viene en el payload (como ya tenías)
            if (! $model->index) {
                $maxIndex = self::where('op_document_id', $model->op_document_id)
                    ->withoutTrashed()
                    ->max('index');
                $model->index = $maxIndex ? $maxIndex + 1 : 1;
            }

            // Defaults (como ya tenías)
            $model->transaction_type_id  ??= 1;
            $model->transaction_status_id ??= 1;
            $model->remmitance_code      ??= null;
        });

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

   
