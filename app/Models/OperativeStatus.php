<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OperativeStatus extends Model
{
    //
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'acronym',
        'description',
    ];

    protected $table = 'operative_statuses'; // ✅ aquí redirigimos la tabla
}
