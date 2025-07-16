<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartnerType extends Model
{
    //
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'acronym',
    ];

    protected $table = 'partner_types'; // ✅ aquí redirigimos la tabla

    public function partners(): HasMany
    {
        return $this->hasMany(Partner::class, 'partner_types_id');
    }
}
