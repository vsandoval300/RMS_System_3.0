<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransactionType extends Model
{
    use SoftDeletes;

    protected $table = 'transaction_types';

    public function transactions(): HasMany
    {
        // FK en transactions: transaction_type_id
        return $this->hasMany(Transaction::class, 'transaction_type_id');
    }
}
