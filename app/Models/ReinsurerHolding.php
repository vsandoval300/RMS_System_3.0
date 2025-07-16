<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReinsurerHolding extends Model
{
    use SoftDeletes;
    
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
