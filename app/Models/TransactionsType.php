<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionsType extends Model
{
    // Si tu tabla se llama distinto, destápalo:
    // protected $table = 'transactions_type_catalog';

    protected $fillable = ['description'];

    // Si añadiste timestamps y/o soft deletes en la migración, déjalos;
    // si tu tabla NO los tiene, desactíalos:
    // public $timestamps = false;
}

