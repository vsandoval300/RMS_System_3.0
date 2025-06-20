<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PartnerTypes extends Model
{
    //
    use HasFactory;
    protected $table = 'partner_types'; // ✅ aquí redirigimos la tabla

    public function partners(): HasMany
    {
        return $this->hasMany(Partners::class, 'partner_types_id');
    }
}
