<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasAuditLogs;

class ReinsurerHolding extends Model
{
    use SoftDeletes, HasAuditLogs;
    
    protected $table = 'holding_reinsurers';

    protected $fillable = [
        'reinsurer_id',
        'holding_id',
        'percentage',
    ];

    /* FK inversas */
    public function reinsurer()
    {
        return $this->belongsTo(Reinsurer::class);
    }

    public function holding()
    {
        return $this->belongsTo(Holding::class);
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
            'holding_id' => Holding::find($value)?->name ?? $value,
            default       => $value,
        };
    }
}
