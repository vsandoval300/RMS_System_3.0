<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasAuditLogs;

class LineOfBusiness extends Model
{
    use HasFactory, SoftDeletes, HasAuditLogs;

    protected $table = 'line_of_businesses';

    protected $fillable = [
        'name',
        'description',
        'risk_covered',
    ];

    /* ─── hasMany ─── */
    public function coverages()
    {
        return $this->hasMany(Coverage::class, 'lob_id');
    }

    /* ─── Metodos para salvar Logs ─── */
    /* ─── Este guarda la etiqueta del campo a manera de identificador ─── */
    protected function getAuditLabelIdentifier(): ?string
    {
        return $this->name
            ?? $this->name . ':'
            ?? parent::getAuditLabelIdentifier();
    }
    
}

