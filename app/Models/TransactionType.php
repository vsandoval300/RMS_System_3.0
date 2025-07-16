<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionType extends Model
{
    use SoftDeletes;
    // Si tu tabla se llama distinto, destápalo:
    // protected $table = 'transactions_type_catalog';

    protected $fillable = ['description'];

    // Si añadiste timestamps y/o soft deletes en la migración, déjalos;
    // si tu tabla NO los tiene, desactíalos:
    // public $timestamps = false;
}

