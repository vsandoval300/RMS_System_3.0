<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Manager extends Model
{
    //
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
    ];

    protected $table = 'managers'; // ✅ aquí redirigimos la tabla
}
