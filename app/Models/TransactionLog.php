<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionLog extends Model
{
    //
    use SoftDeletes;

    protected $table = 'transaction_logs';

    /** 🟢 Declaración correcta para IDs tipo string */
    protected $primaryKey = 'id';
    public $incrementing = false;     // 👈 ID no es autoincremental
    protected $keyType = 'string';    // 👈 ID es string (varchar)

    protected $fillable = [
        'transaction_code',   // 👈 clave que enlaza con transactions.remmitance_code
        'index',
        'deduction_type',
        'from_entity',
        'to_entity',
        'sent_date',
        'received_date',
        'exch_rate',
        'gross_amount',
        'commission_discount',
        'banking_fee',
        'net_amount',
        'status',
    ];

    protected $casts = [
        'sent_date'      => 'datetime',
        'received_date'  => 'datetime',
        'exch_rate'      => 'decimal:6',
        'gross_amount'   => 'decimal:2',
        'commission_discount' => 'decimal:2',
        'banking_fee'    => 'decimal:2',
        'net_amount'     => 'decimal:2',
    ];

    // ✅ inversa, también por código
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_code');
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
