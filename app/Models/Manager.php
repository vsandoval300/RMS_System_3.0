<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manager extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    protected $table = 'managers'; // ✅ aquí redirigimos la tabla
}
