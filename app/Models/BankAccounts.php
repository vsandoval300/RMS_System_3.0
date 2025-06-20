<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankAccounts extends Model
{
    //
    use HasFactory;

    public function currency(): BelongsTo
    {
    return $this->belongsTo(Currencies::class, 'currency_id');
    }


    public function bank(): BelongsTo
    {
        return $this->belongsTo(Banks::class);
    }

    public function bank_inter(): BelongsTo
    {
        return $this->belongsTo(Banks::class, 'intermediary_bank');
    }
    

}
