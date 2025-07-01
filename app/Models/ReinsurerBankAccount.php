<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReinsurerBankAccount extends Model
{
    //
    use HasFactory;

    protected $table = 'reinsurer_bankaccounts';

    protected $fillable = [
        'reinsurer_id',
        'bank_account_id',
        // 'extra_column',
    ];

    public function reinsurer(): BelongsTo
    {
        return $this->belongsTo(Reinsurer::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }
}
