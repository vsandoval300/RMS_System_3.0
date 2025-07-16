<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    //
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'short_name',
        'description',
        'webpage',
        'logo_path',
        'country_id',
    ];

    /* ───────── Relaciones ───────── */

    // País
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    // Industrias (many-to-many)
    public function industries(): BelongsToMany
    {
        return $this->belongsToMany(
            Industry::class,     // Modelo relacionado
            'client_industries', // Tabla pivote (si se llama distinto, cámbialo)
            'client_id',         // FK de este modelo en la pivote
            'industry_id'        // FK del modelo relacionado en la pivote
        )
        ->withTimestamps()            // si tu pivote tiene created_at / updated_at
        // ->withPivot(['extra_col'])    // si guardas columnas adicionales
        ;
    }
}
