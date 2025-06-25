<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'name',
        'acronym',
        'activity',
        'webpage',
        'industry_id',
        'country_id',
    ];



    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function sector(): BelongsTo
    {
    return $this->belongsTo(Industry::class, 'industry_id');
    }

}
