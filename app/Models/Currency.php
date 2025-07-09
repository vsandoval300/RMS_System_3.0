<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    //
     use HasFactory;

    protected $fillable = [
        'name',
        'acronym',
    ];

    /* ---------------------------------------------------
     |  ➜  Relaciones hasMany
     ---------------------------------------------------*/
    public function bankaccounts(): HasMany
    {
        return $this->hasMany(bankAccount::class);
    }

    public function businesses()
    {
        return $this->hasMany(Business::class);
    }

}
