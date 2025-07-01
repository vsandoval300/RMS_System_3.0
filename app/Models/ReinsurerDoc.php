<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReinsurerDoc extends Model
{
    protected $table = 'reinsurer_docs';

    protected $fillable = [
        'stamp_date',
        'document_path',
        'document_type_id',
        'reinsurer_id',
        // 'reinsurer_id' lo rellena Filament al crear desde el RM
    ];

    // Relación inversa
    public function reinsurer(): BelongsTo
    {
        return $this->belongsTo(Reinsurer::class);
    }

    // Relación inversa con el tipo de documento
    public function documentCorpType(): BelongsTo
    {
        return $this->belongsTo(DocumentCorpType::class, 'document_type_id');
    }
}
