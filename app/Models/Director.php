<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Occupation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\HasAuditLogs;
use App\Models\Country; 

class Director extends Model
{
    //
    use SoftDeletes,HasAuditLogs;

    protected $fillable = [
        'name','surname','gender','email','phone','address',
        'occupation','image','country_id',
    ];

    public function boards(): BelongsToMany
    {
        return $this->belongsToMany(Board::class, 'board_directors')
                    ->withTimestamps();
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->name} {$this->surname}");
    }

     public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
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
                           
}
