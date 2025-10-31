<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Manager extends Model
{
    //
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'country_id',
    ];

    protected $table = 'managers'; // ✅ aquí redirigimos la tabla

    // País
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
