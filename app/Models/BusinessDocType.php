<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessDocType extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    protected $table = 'business_doc_types'; // ✅ aquí redirigimos la tabla
}
