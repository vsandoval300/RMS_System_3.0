<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subregion extends Model
{
    //
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'subregion_code',
        'region_id',
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
}
