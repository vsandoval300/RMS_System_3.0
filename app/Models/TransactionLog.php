<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TransactionLog extends Model
{
    use SoftDeletes;

    protected $table = 'transaction_logs';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

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

        // ❌ NO incluir (son GENERATED):
        // 'gross_amount_calc',
        // 'commission_discount',
        // 'net_amount',

        'banking_fee',
        'evidence_path',
    ];

    protected $casts = [
        'sent_date'     => 'date',
        'received_date' => 'date',

        'exch_rate'              => 'decimal:6',
        'proportion'             => 'decimal:6',
        'commission_percentage'  => 'decimal:6',

        'gross_amount'      => 'decimal:2',
        'gross_amount_calc' => 'decimal:2',
        'commission_discount' => 'decimal:2',
        'banking_fee'       => 'decimal:2',
        'net_amount'        => 'decimal:2',
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
}
