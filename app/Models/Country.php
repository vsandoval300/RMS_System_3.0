<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'name',
        'alpha_2',
        'alpha_3',
        'country_code',
        'iso_code',
        'am_best_code',
        'latitude',
        'longitude',
        'region_id',
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function companie(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
