<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentCorpType extends Model
{
    // ← solo necesitas esto si la tabla NO sigue la convención
    use SoftDeletes;
    
    protected $table = 'document_types';

    protected $fillable = [
        'name',
        'acronym',
        'description',
    ];

    /**
     * Un tipo de documento puede tener muchos documentos subidos por reaseguradores.
     */
    public function reinsurerDocs(): HasMany
    {
        return $this->hasMany(ReinsurerDoc::class, 'document_type_id');
    }
}