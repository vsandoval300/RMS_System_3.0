<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;   // 👈 importa Pivot

class ReinsurerBoard extends Pivot
{
    protected $table = 'reinsurer_boards';

    /** Si tu tabla pivot SÍ tiene columna id autoincremental */
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
}