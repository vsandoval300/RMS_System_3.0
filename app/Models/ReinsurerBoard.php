<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;   // 👈 importa Pivot
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReinsurerBoard extends Pivot
{
    use SoftDeletes;
    
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
}