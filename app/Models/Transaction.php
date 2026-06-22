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
use App\Services\TransactionLogsPreviewService;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Enums\TransactionLifecycleStatus;
use Illuminate\Support\Carbon;
use App\Services\TransactionLogsBuilderService;
use App\Models\TransactionRecalculation;

class Transaction extends Model
{
    use SoftDeletes, HasAuditLogs;

    public static bool $autoBuildLogs = true;

    /* ---------------------------------------------------
     |  Tabla y PK
     ---------------------------------------------------*/
    protected $table      = 'transactions';
    protected $primaryKey = 'id';

    // ✅ UUID en Eloquent se maneja como string
    public $incrementing = false;
    protected $keyType = 'string';

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
        'amount',
    ];

    protected $casts = [
        'due_date'    => 'date',
        'proportion'  => 'decimal:6',
        'exch_rate'   => 'decimal:10',
        'amount'      => 'decimal:2',
    ];

    /* --------------------------------------------------
     |  belongsTo
     --------------------------------------------------*/
    public function type(): BelongsTo
    {
        return $this->belongsTo(TransactionType::class, 'transaction_type_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(TransactionStatus::class, 'transaction_status_id');
    }

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
        return $this->hasMany(TransactionLog::class, 'transaction_id');
    }

    public function supports(): HasMany
    {
        return $this->hasMany(TransactionSupport::class, 'transaction_id');
    }

    public function recalculations(): HasMany
    {
        return $this->hasMany(TransactionRecalculation::class, 'transaction_id');
    }

    public function lastLog(): HasOne
    {
        return $this->hasOne(TransactionLog::class, 'transaction_id')
            ->ofMany([
                'index' => 'max',
                'created_at' => 'max', // desempate (timestamp sí soporta max)
            ]);
    }


    /* --------------------------------------------------
     |  Audit helpers
     --------------------------------------------------*/
    protected function getAuditOwnerModel(): Model
    {
        return $this;
    }

    protected function getAuditLabelIdentifier(): ?string
    {
        $docId = $this->operativeDoc?->id;

        if ($docId && $this->index) {
            return "{$docId}-TX" . str_pad($this->index, 2, '0', STR_PAD_LEFT);
        }

        return parent::getAuditLabelIdentifier();
    }

    public function auditLabel(): string
    {
        // Reutiliza tu lógica actual
        $docId = $this->operativeDoc?->id;

        if ($docId && $this->index) {
            return "{$docId}-TX" . str_pad((string) $this->index, 2, '0', STR_PAD_LEFT);
        }

        // fallback
        return (string) $this->id;
    }

    /* --------------------------------------------------
     |  Booted
     --------------------------------------------------*/
    protected static function booted(): void
    {
        static::creating(function (self $model) {
            // ✅ UUID
            if (! $model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            } else {
                $exists = self::withTrashed()->whereKey($model->getKey())->exists();
                if ($exists) {
                    $model->{$model->getKeyName()} = (string) Str::uuid();
                }
            }

            // ✅ Index consecutivo por documento
            if (! $model->index) {
                $maxIndex = self::where('op_document_id', $model->op_document_id)->max('index');
                $model->index = ($maxIndex ?? 0) + 1;
            }

            // ✅ Defaults
            $model->transaction_type_id   ??= 1;
            $model->transaction_status_id ??= 1; // Pending
            $model->remmitance_code       ??= null;
        });


        /**
         * ✅ Crear logs al crear Transaction
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

        'index'          => $row['index'] ?? null,
        'proportion'     => (string) ($row['proportion'] ?? $tx->proportion),
        'exch_rate'      => (string) ($row['exchange_rate'] ?? $row['exch_rate'] ?? $tx->exch_rate),

        'deduction_type' => $row['deduction_id'] ?? $row['deduction_type'] ?? null,
        'from_entity'    => $row['from_entity'] ?? null,
        'to_entity'      => $row['to_entity'] ?? null,

        'commission_percentage' => (string) ($row['commission_percentage'] ?? 0),

        // valores que vienen del Transaction Cycle / Preview
        'gross_amount'       => (string) ($row['gross_amount'] ?? 0),
        'gross_amount_calc'  => (string) ($row['gross_amount_calc'] ?? $row['gross_amount'] ?? 0),
        'commission_discount'=> (string) ($row['commission_discount'] ?? $row['discount'] ?? 0),
        'banking_fee'        => (string) ($row['banking_fee'] ?? 0),
        'net_amount'         => (string) ($row['net_amount'] ?? 0),

        // status inicial
        'status'             => $row['status'] ?? 'Pending',

        'sent_date'      => null,
        'received_date'  => null,

        'created_at'     => $now,
        'updated_at'     => $now,
    ];
})->all();

                logger()->info('TX LOGS DEBUG', [
                    'transaction_id' => $tx->id,
                    'tx_proportion' => $tx->proportion,
                    'tx_exch_rate' => $tx->exch_rate,
                    'rows_from_service' => $rows,
                    'payload_inserted' => $payload,
                ]);

                TransactionLog::insert($payload);
            });
        });



        static::deleted(function (self $model) {
            // ✅ Soft delete de logs relacionados
            $model->logs()->delete();

            // ✅ Reordenar las transacciones restantes del mismo documento
            self::where('op_document_id', $model->op_document_id)
                ->orderBy('index')
                ->get()
                ->values()
                ->each(function ($record, $key) {
                    $record->index = $key + 1;
                    $record->saveQuietly();
                });
        });

        static::restored(function (self $model) {
            $model->logs()->withTrashed()->restore();
        });

        static::forceDeleted(function (self $model) {
            $model->logs()->withTrashed()->forceDelete();
        });


    }


    public function resolveTransactionStatus(?Carbon $today = null): TransactionLifecycleStatus
    {
        $logs = $this->logs()
            ->withoutTrashed()
            ->orderBy('index')
            ->get();

        if ($logs->isEmpty()) {
            return TransactionLifecycleStatus::PENDING;
        }

        $lastLog = $logs->last();

        // ✅ Si el último log está completado, toda la transacción se considera completada
        if ($lastLog && $lastLog->status === 'Completed') {
            return TransactionLifecycleStatus::COMPLETED;
        }

        $statuses = $logs->pluck('status');

        if ($statuses->contains('In process')) {
            return TransactionLifecycleStatus::IN_PROCESS;
        }

        if ($statuses->contains('Completed')) {
            return TransactionLifecycleStatus::IN_PROCESS;
        }

        return TransactionLifecycleStatus::PENDING;
    }


    public function lifecycleProgressPercentage(): int
    {
        $logs = $this->logs()
            ->withoutTrashed()
            ->orderBy('index')
            ->get();

        if ($logs->isEmpty()) {
            return 0;
        }

        $lastLog = $logs->last();

        if ($lastLog && $lastLog->status === 'Completed') {
            return 100;
        }

        $completed = $logs
            ->where('status', 'Completed')
            ->count();

        return (int) round(($completed / $logs->count()) * 100);
    }
}
