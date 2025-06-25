<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Partner extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'name',
        'short_name',
        'acronym',
        'partner_types_id',
        'country_id',
    ];


    public function partnerType(): BelongsTo
    {
    return $this->belongsTo(PartnerType::class, 'partner_types_id');
    }
    
    public function country(): BelongsTo
    {
    return $this->belongsTo(Country::class, 'country_id');
    }
}
