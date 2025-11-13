<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Country; 
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasAuditLogs;

class Holding extends Model
{
    use HasFactory, SoftDeletes, HasAuditLogs;
    
    protected $fillable = [
            'name',
            'short_name',
            'country_id',
            'client_id',
    ];

    public function reinsurerHoldings(): HasMany
    {
        return $this->hasMany(ReinsurerHolding::class);
    }

    public function reinsurers(): BelongsToMany
    {
        return $this->belongsToMany(
                Reinsurer::class,
                'holding_reinsurers'
            )
            ->using(ReinsurerHolding::class)
            ->withPivot(['id', 'percentage'])
            ->withTimestamps();
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
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
            'client_id' => Client::find($value)?->name ?? $value,
            default       => $value,
        };
    }
}
