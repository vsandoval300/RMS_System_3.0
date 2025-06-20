<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Companies extends Model
{
    //
    use HasFactory;
    public function country(): BelongsTo
    {
        return $this->belongsTo(Countries::class);
    }

    public function sector(): BelongsTo
    {
    return $this->belongsTo(Sectors::class, 'industry_id');
    }

}
