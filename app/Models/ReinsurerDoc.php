<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasAuditLogs;

class ReinsurerDoc extends Model
{
    use SoftDeletes, HasAuditLogs;

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

    /* ─── Metodos para salvar Logs ─── */
    /* ─── Este guarda la etiqueta del campo a manera de identificador ─── */
    protected function getAuditOwnerModel(): Model
    {
        return $this->reinsurer ?? $this;
    }

    protected function getAuditLabelIdentifier(): ?string
    {
        return $this->name
            ?? $this->name . ':'
            ?? parent::getAuditLabelIdentifier();
    }

    protected function transformAuditValue(string $field, $value)
    {
        if ($value === null || $value === '') {
            return $value;
        }

        return match ($field) {
            'document_type_id' => DocumentType::find($value)?->name ?? $value,
            default       => $value,
        };
    }




}
