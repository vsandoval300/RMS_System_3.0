<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentType extends Model
{
    //
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'acronym',
        'description',
    ];

    protected $table = 'document_types'; // ✅ aquí redirigimos la tabla
}
