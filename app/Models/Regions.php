<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Regions extends Model
{
    //
    use HasFactory;
    public function subregions(): HasMany
    {
        return $this->hasMany(Subregions::class);
    }
}
