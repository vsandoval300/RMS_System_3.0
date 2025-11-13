<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;   // 👈 importa Pivot
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\HasAuditLogs;

class ReinsurerBoard extends Pivot
{
    use SoftDeletes,HasAuditLogs;
    
    protected $table = 'reinsurer_boards';
    public $incrementing = true;

    protected $fillable = [
        'reinsurer_id',
        'board_id',
        'appt_date',
    ];

    /* Relaciones inversas (opcionales pero útiles) */
    public function reinsurer()
    {
        return $this->belongsTo(Reinsurer::class);
    }

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    /* ─── Metodos para salvar Logs ─── */
    /* ─── Este guarda la etiqueta del campo a manera de identificador ─── */
    protected function getAuditOwnerModel(): Model
    {
        return $this->reinsurer ?? $this;
    }

    protected function getAuditLabelIdentifier(): ?string
    {
        // Cargamos board + directors (si no están ya cargados)
        $board = $this->board()->with('directors')->first();

        if (! $board) {
            // Fallback si algo raro pasa
            return 'Board pivot #' . ($this->id ?? '?');
        }

        $boardIndex = $board->index ?? $board->id;

        // Concatenamos "Nombre Apellido" para cada director
        $directors = $board->directors
            ->map(fn ($d) => trim($d->name . ' ' . $d->surname))
            ->filter()                    // quita vacíos
            ->implode(', ');              // "A B, C D, ..."

        if ($directors === '') {
            return "Board {$boardIndex}";
        }

        return "Board {$boardIndex} ({$directors})";
    }

    protected function transformAuditValue(string $field, $value)
    {
        if ($value === null || $value === '') {
            return $value;
        }

        return match ($field) {
            // si algún día cambia el board_id, mostramos su index
            'board_id' => Board::find($value)?->index ?? $value,
            default    => $value,
        };
    }
}