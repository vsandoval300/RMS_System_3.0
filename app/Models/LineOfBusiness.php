<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineOfBusiness extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    protected $table = 'line_of_businesses'; // ✅ aquí redirigimos la tabla
}
