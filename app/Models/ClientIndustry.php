<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasAuditLogs;
use App\Models\Industry; 
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Model as EloquentModel;


class ClientIndustry extends Pivot
{
    //
    use HasFactory, HasAuditLogs;

    protected $table = 'client_industries';

    protected $fillable = [
        'client_id',
        'industry_id',
        // 'extra_column',
    ];

    // 👉 solo si tu tabla tiene columna 'id' autoincremental:
    public $incrementing = true;
    protected $primaryKey = 'id';
    protected $keyType = 'int'; // o 'string' si fuera uuid



    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }

    /* ──────────────  Metods for audit registers  ──────────────── */
    protected function getAuditOwnerModel(): Model
    {
        return $this->client ?? $this;
    }

    // Etiqueta amigable en el historial
    protected function getAuditLabelIdentifier(): ?string
    {
        if ($this->industry) {
            return 'Industry: ' . $this->industry->name;
        }

        return parent::getAuditLabelIdentifier();
    }

    /**
     * 🔴 Aquí “traducimos” los eventos created/deleted a Attached/Detached
     * SOLO para este pivote.
     */
    protected function getAuditEventLabelPrefix(string $event): string
    {
        return match ($event) {
            'created' => 'Attached',
            'deleted' => 'Detached',
            default   => parent::getAuditEventLabelPrefix($event),
        };
    }

    /* ────────────────────────────────────────────────────────────── */
}
