<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use App\Models\Traits\HasAuditLogs;

class TransactionLog extends Model
{
    use SoftDeletes, HasAuditLogs;

    protected $table = 'transaction_logs';
    protected $primaryKey = 'id';
    public $incrementing = false;

    // ✅ UUID se maneja como string
    protected $keyType = 'string';

     // ✅ IMPORTANTE para "tocar" al padre al modificar un log
    protected $touches = ['transaction'];

    protected $fillable = [
        'id',
        'transaction_id',
        'index',
        'deduction_type',
        'from_entity',
        'to_entity',
        'sent_date',
        'received_date',
        'exch_rate',
        'proportion',
        'commission_percentage',
        'gross_amount',

        // GENERATED en DB:
        // 'gross_amount_calc',
        // 'commission_discount',
        // 'net_amount',

        'banking_fee',
        'evidence_path',
    ];

    protected $casts = [
        'sent_date'     => 'date',
        'received_date' => 'date',

        // ✅ OJO: aquí puedes poner la precisión real de tu DB (18,10 etc.)
        'exch_rate'             => 'decimal:10',
        'proportion'            => 'decimal:6',
        'commission_percentage' => 'decimal:6',

        'gross_amount'         => 'decimal:2',
        'gross_amount_calc'    => 'decimal:2',
        'commission_discount'  => 'decimal:2',
        'banking_fee'          => 'decimal:2',
        'net_amount'           => 'decimal:2',

        'status' => 'string',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (blank($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }


    /* --------------------------------------------------
     |  Relations
     --------------------------------------------------*/
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function deduction(): BelongsTo
    {
        return $this->belongsTo(Deduction::class, 'deduction_type', 'id');
    }

    public function fromPartner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'from_entity', 'id');
    }

    public function toPartner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'to_entity', 'id');
    }

    /* --------------------------------------------------
     |  Audit helpers
     --------------------------------------------------*/

    // ✅ Log del hijo se guarda “bajo” el Transaction padre
    protected function getAuditOwnerModel(): Model
    {
        return $this->transaction ?? $this;
    }

    // ✅ Identificador legible en el Audit Info
    protected function getAuditLabelIdentifier(): ?string
    {
        // Ej: OPDOC-TX03 / LOG-02
        $txLabel = $this->transaction?->auditLabel();
        $idx = $this->index ?? null;

        if ($txLabel && $idx) {
            return "{$txLabel} / LOG-" . str_pad((string) $idx, 2, '0', STR_PAD_LEFT);
        }

        return $txLabel ?? parent::getAuditLabelIdentifier();
    }





}
