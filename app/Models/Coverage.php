<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\LineOfBusiness;
use App\Models\Traits\HasAuditLogs;

class Coverage extends Model
{
    use HasFactory, SoftDeletes, HasAuditLogs;

    protected $table = 'coverages';

    protected $fillable = [
        'name',
        'acronym',
        'description',
        'lob_id',           // FK → line_of_businesses.id
    ];

    /* ─── belongsTo ─── */
    public function lineOfBusiness()
    {
        return $this->belongsTo(LineOfBusiness::class, 'lob_id');
    }

    /* ─── hasMany ─── */
    public function insuredDocs()
    {
        return $this->hasMany(BusinessOpDocsInsured::class, 'coverage_id');
    }

    public function liabilityStructures()
    {
        return $this->hasMany(LiabilityStructure::class, 'coverage_id');
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
            'lob_id' => LineOfBusiness::find($value)?->name ?? $value,
            default       => $value,
        };
    }
}
