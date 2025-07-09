<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'name',
        'region_code',
    ];

    /* ---------------------------------------------------
     |  ➜  Relaciones hasMany
     ---------------------------------------------------*/
    public function subregions(): HasMany
    {
        return $this->hasMany(Subregion::class);
    }

    public function businesses()
    {
        return $this->hasMany(Business::class);
    }

}
