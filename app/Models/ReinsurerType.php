<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReinsurerType extends Model
{
    //
    use HasFactory;
    protected $table = 'reinsurer_types'; // ✅ aquí redirigimos la tabla
}
