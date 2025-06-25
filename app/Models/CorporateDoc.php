<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorporateDoc extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'name',
        'acronym',
        'description',
    ];

    protected $table = 'document_types'; // ✅ aquí redirigimos la tabla
}
