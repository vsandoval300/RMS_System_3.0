<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasAuditLogs;

class Industry extends Model
{
    //
    use HasFactory, SoftDeletes, HasAuditLogs;

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

    /* ─── Metodos para salvar Logs ─── */
    /* ─── Este guarda la etiqueta del campo a manera de identificador ─── */
    protected function getAuditLabelIdentifier(): ?string
    {
        return $this->name
            ?? $this->name . ':'
            ?? parent::getAuditLabelIdentifier();
    }

}
