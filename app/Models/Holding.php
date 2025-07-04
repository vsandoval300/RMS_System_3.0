<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Holding extends Model
{
    protected $fillable = [
            'name',
            'short_name',
            'country_id',
            'client_id',
    ];

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

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
