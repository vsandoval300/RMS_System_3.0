<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'name',
        'short_name',
        'description',
        'webpage',
        'logo_path',
        'country_id',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

     public function sectors(): HasMany
    {
        return $this->hasMany(ClientIndustry::class);
    }
}
