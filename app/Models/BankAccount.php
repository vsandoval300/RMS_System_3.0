<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankAccount extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'beneficiary_acct_name',
        'beneficiary_address',
        'beneficiary_swift',
        'beneficiary_acct_no',
        'ffc_acct_name',
        'ffc_acct_no',
        'ffc_acct_address',
        'status_account',
        'currency_id',
        'bank_id',
        'intermediary_bank',
    ];


    public function currency(): BelongsTo
    {
    return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function bank_inter(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'intermediary_bank');
    }
    

}
