<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasAuditLogs;

class Company extends Model
{
    //
    use HasFactory, SoftDeletes, HasAuditLogs;

    protected $table = 'companies';

    protected $fillable = [
        'name',
        'acronym',
        'activity',
        'webpage',
        'industry_id',
        'country_id',
    ];



    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function sector(): BelongsTo
    {
    return $this->belongsTo(Industry::class, 'industry_id');
    }

    /* ─── hasMany ─── */
    public function insuredDocs()
    {
        return $this->hasMany(BusinessOpDocsInsured::class, 'company_id');
    }

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
            'industry_id' => Industry::find($value)?->name ?? $value,
            'country_id'  => Country::find($value)?->name ?? $value,
            default       => $value,
        };
    }

}
