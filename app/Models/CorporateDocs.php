<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorporateDocs extends Model
{
    //
    //
    use HasFactory;
    protected $table = 'document_types'; // ✅ aquí redirigimos la tabla
}
