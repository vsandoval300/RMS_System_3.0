<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReinsurerHolding extends Model
{
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
}
