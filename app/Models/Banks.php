<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Banks extends Model
{
    //
    use HasFactory;

    public function bankaccounts(): HasMany
    {
        return $this->hasMany(bankAccounts::class);
    }

    
}
