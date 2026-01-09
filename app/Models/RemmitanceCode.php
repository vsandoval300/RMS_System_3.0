<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RemmitanceCode extends Model
{
    use SoftDeletes;

    /* ---------------------------------------------------
     |  Tabla
     ---------------------------------------------------*/
    protected $table = 'remmitance_codes';

    /* ---------------------------------------------------
     |  Primary Key
     ---------------------------------------------------*/
    protected $primaryKey = 'remmitance_code';
    public $incrementing = false;      // 👈 no es autoincremental
    protected $keyType = 'string';     // 👈 es string (varchar)

    /* ---------------------------------------------------
     |  Mass Assignment
     ---------------------------------------------------*/
    protected $fillable = [
        'remmitance_code',
        'id',
    ];

    /* ---------------------------------------------------
     |  Casts
     ---------------------------------------------------*/
    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    /* ---------------------------------------------------
     |  Relation with Transaction
     ---------------------------------------------------*/
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'remmitance_code', 'remmitance_code');
    }

}
