<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\Traits\HasAuditLogs;
use Illuminate\Support\Facades\DB;
use App\Models\OperativeDoc;
use App\Models\TransactionLog;
use App\Models\CostNodex; // ajusta al nombre real
use App\Services\TransactionLogsPreviewService;

class Transaction extends Model
{
    //
    
    use SoftDeletes, HasAuditLogs;

    public static bool $autoBuildLogs = true;
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

    public function remmitanceCode(): BelongsTo
    {
        return $this->belongsTo(RemmitanceCode::class, 'remmitance_code', 'remmitance_code');
    }


    /* --------------------------------------------------
     |  hasMany
     --------------------------------------------------*/
    public function logs(): HasMany
    {
        // Enlaza por código: transaction_logs.transaction_code → transactions.remmitance_code
        return $this->hasMany(TransactionLog::class, 'transaction_id');
    }


    /**
     * Queremos que los logs aparezcan en el Business (como los demás).
     */
    protected function getAuditOwnerModel(): Model
    {
        return $this->operativeDoc?->business
            ?? $this->operativeDoc
            ?? $this;
    }

    /**
     * Cómo se va a ver el identificador en el event:
     * p.ej. "Updated Transaction 2025-1MO054-001-01"
     */
    protected function getAuditLabelIdentifier(): ?string
    {
        $docId = $this->operativeDoc?->id;

        if ($docId && $this->index) {
            return "{$docId}-TX" . str_pad($this->index, 2, '0', STR_PAD_LEFT);
        }

        return parent::getAuditLabelIdentifier();
    }

    

        protected static function booted()
        {
            static::creating(function ($model) {
                // UUID
                if (! $model->getKey()) {
                    $model->{$model->getKeyName()} = (string) Str::uuid();
                } else {
                    $exists = self::withTrashed()->whereKey($model->getKey())->exists();
                    if ($exists) {
                        $model->{$model->getKeyName()} = (string) Str::uuid();
                    }
                }

                // Index consecutivo por documento
                if (! $model->index) {
                    $maxIndex = self::where('op_document_id', $model->op_document_id)->max('index');
                    $model->index = ($maxIndex ?? 0) + 1;
                }

                // Defaults
                $model->transaction_type_id   ??= 1;
                $model->transaction_status_id ??= 1;
                $model->remmitance_code       ??= null;
            });

            /**
             * ✅ DUEÑO de la lógica: al crear Transaction → generar TransactionLogs
             */
            static::created(function (self $tx) {

                if (! static::$autoBuildLogs) {
                    return;
                }

                DB::transaction(function () use ($tx) {
                    TransactionLog::where('transaction_id', $tx->id)->forceDelete();

                    $rows = app(TransactionLogsPreviewService::class)->build(
                        opDocumentId: (string) $tx->op_document_id,
                        typeId: (int) $tx->transaction_type_id,
                        proportion: (float) $tx->proportion,
                        exchRate: (float) $tx->exch_rate,
                        remittanceCode: $tx->remmitance_code,
                        dueDate: $tx->due_date,
                    );

                    if (empty($rows)) {
                        return;
                    }

                    $now = now();

                    $payload = collect($rows)->map(function (array $row) use ($tx, $now) {
                        return [
                            'id'             => (string) Str::uuid(),
                            'transaction_id' => $tx->id,
                            'index'          => (int) ($row['index'] ?? 1),
                            'deduction_type' => (int) ($row['deduction_id'] ?? $row['deduction_type'] ?? 1),
                            'from_entity'    => (int) ($row['from_entity'] ?? 0),
                            'to_entity'      => (int) ($row['to_entity'] ?? 0),
                            'sent_date'      => null,
                            'received_date'  => null,
                            'exch_rate'      => (float) $tx->exch_rate,
                            'proportion'     => (float) $tx->proportion,
                            'commission_percentage' => (string) ($row['commission_percentage'] ?? 0),
                            'gross_amount'   => (float) ($row['gross_amount'] ?? 0),
                            'banking_fee'    => 0,
                            'created_at'     => $now,
                            'updated_at'     => $now,
                        ];
                    })->all();

                    TransactionLog::insert($payload);
                });
            });

            static::deleted(function ($model) {
                self::where('op_document_id', $model->op_document_id)
                    ->orderBy('index')
                    ->get()
                    ->values()
                    ->each(function ($record, $key) {
                        $record->index = $key + 1;
                        $record->saveQuietly();
                    });
            });
        }

    
}

   
