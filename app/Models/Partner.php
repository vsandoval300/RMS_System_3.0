<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasAuditLogs;

class Partner extends Model
{
    //
    use HasFactory, SoftDeletes, HasAuditLogs;

    protected $fillable = [
        'name',
        'short_name',
        'acronym',
        'partner_types_id',
        'country_id',
    ];

    /* ---------------------------------------------------
     |  ➜  Relaciones belongsTo
     ---------------------------------------------------*/
    public function partnerType(): BelongsTo
    {
        return $this->belongsTo(PartnerType::class, 'partner_types_id');
    }
    
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /* ---------------------------------------------------
     |  ➜  Relaciones hasMany
     ---------------------------------------------------*/
    public function businesses()
    {
        return $this->hasMany(Business::class, 'producer_id');
    }


    public function logsFrom()
    {
        return $this->hasMany(TransactionLog::class, 'from_entity', 'id');
    }

    public function logsTo()
    {
        return $this->hasMany(TransactionLog::class, 'to_entity', 'id');
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
            'partner_types_id' => PartnerType::find($value)?->name ?? $value,
            'country_id'  => Country::find($value)?->name ?? $value,
            default       => $value,
        };
    }


}
