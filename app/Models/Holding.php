<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Holding extends Model
{
    public function reinsurerHoldings(): HasMany
    {
        return $this->hasMany(ReinsurerHolding::class);
    }

    public function reinsurers(): BelongsToMany
    {
        return $this->belongsToMany(
                Reinsurer::class,
                'holding_reinsurers'
            )
            ->using(ReinsurerHolding::class)
            ->withPivot(['id', 'percentage'])
            ->withTimestamps();
    }
}
