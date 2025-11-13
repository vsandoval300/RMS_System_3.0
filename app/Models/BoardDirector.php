<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\HasAuditLogs;


class BoardDirector extends Model
{
    //
    use HasFactory, SoftDeletes, HasAuditLogs;

    protected $table = 'board_directors';
    protected $fillable = ['board_id', 'director_id'];

    public function director(): BelongsTo
    {
        return $this->belongsTo(Director::class);
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
            'director_id' => Director::find($value)?->name ?? $value,
            default       => $value,
        };
    }
}
