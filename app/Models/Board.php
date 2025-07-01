<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Board extends Model
{
    // ───────── Relaciones pivot (Board ⇄ Reinsurer) ─────────

    /**
     * Fila pivot individual (con appt_date)
     */
    public function reinsurerBoards(): HasMany
    {
        return $this->hasMany(ReinsurerBoard::class);
    }

    /**
     * Muchos-a-muchos simplificado: lista de reinsurers
     */
    public function reinsurers(): BelongsToMany
    {
        return $this->belongsToMany(
                Reinsurer::class,
                'reinsurer_boards'
            )
            ->using(ReinsurerBoard::class)
            ->withPivot(['id', 'appt_date'])
            ->withTimestamps();
    }
}