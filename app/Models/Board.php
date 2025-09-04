<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Director;      // 👈  IMPORTANTE
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Board extends Model
{
    
    use HasFactory, SoftDeletes;
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

   public function directors(): BelongsToMany   // 👈  RELACIÓN
    {
        return $this->belongsToMany(
            Director::class,
            'board_directors'
        )->withTimestamps();
    }

    public function boardDirectors(): HasMany
    {
        return $this->hasMany(BoardDirector::class, 'board_id');
    }
}