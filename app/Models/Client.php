<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Country; 
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasAuditLogs;
use App\Models\ClientIndustry;
use App\Models\Industry;

class Client extends Model
{
    //
    use HasFactory, SoftDeletes, HasAuditLogs;

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
        ->using(ClientIndustry::class)
        ->withPivot('id')       // 👈 solo el id del pivote (o lo que necesites)
        ->withTimestamps();     // sin wherePivotNull
    }



    /* ─── Metodos para salvar Logs ─── */
    /* ─── Este guarda la etiqueta del campo a manera de identificador ─── */
    protected function getAuditLabelIdentifier(): ?string
    {
        return $this->name
            ?? $this->name . ':'
            ?? parent::getAuditLabelIdentifier();
    }

    protected function transformAuditValue(string $field, $value)
    {
        if ($value === null || $value === '') {
            return $value;
        }

        return match ($field) {
            'country_id' => Country::find($value)?->name ?? $value,
            default       => $value,
        };
    }

    protected static function booted(): void
    {
        static::deleting(function (Client $client) {
            // borrar físicamente los pivotes cuando se borra el cliente
            ClientIndustry::where('client_id', $client->id)->delete();
        });
    }
}
