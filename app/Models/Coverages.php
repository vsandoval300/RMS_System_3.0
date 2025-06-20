<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coverages extends Model
{
    //
    use HasFactory;
    public function lineOfBusiness(): BelongsTo
    {
    return $this->belongsTo(LineOfBusiness::class, 'lob_id');
    }
}
