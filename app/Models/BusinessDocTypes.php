<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessDocTypes extends Model
{
    //
    use HasFactory;
    protected $table = 'business_doc_types'; // ✅ aquí redirigimos la tabla
}
