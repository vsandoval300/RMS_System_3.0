<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coverage extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'name',
        'acronym',
        'description',
        'lob_id',
    ];

    public function lineOfBusiness(): BelongsTo
    {
    return $this->belongsTo(LineOfBusiness::class, 'lob_id');
    }
}
