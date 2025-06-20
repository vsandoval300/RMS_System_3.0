<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Partners extends Model
{
    //
    public function partnerType(): BelongsTo
    {
    return $this->belongsTo(PartnerTypes::class, 'partner_types_id');
    }
    
    public function country(): BelongsTo
    {
    return $this->belongsTo(Countries::class, 'country_id');
    }
}
