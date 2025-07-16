<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReinsurerType extends Model
{
    //
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type_acronym',
        'description',
    ];

    protected $table = 'reinsurer_types'; // ✅ aquí redirigimos la tabla
}
