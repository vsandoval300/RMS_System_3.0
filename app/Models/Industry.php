<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Industry extends Model
{
    //
    use HasFactory;

    protected $table = 'industries'; // ✅ aquí redirigimos la tabla

    protected $fillable = [
        'name',
        'description',
    ];


    public function companie(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /* ───────── Relaciones ───────── */

    // Clientes que pertenecen a esta industria
    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(
            Client::class,
            'client_industries',
            'industry_id',
            'client_id'
        );
    }

}
