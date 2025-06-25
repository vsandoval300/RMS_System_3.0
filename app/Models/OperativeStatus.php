<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperativeStatus extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'acronym',
        'description',
    ];

    protected $table = 'operative_statuses'; // ✅ aquí redirigimos la tabla
}
