<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Countries extends Model
{
    //
    use HasFactory;
    public function region(): BelongsTo
    {
        return $this->belongsTo(Regions::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Clients::class);
    }
}
