<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coverage extends Model
{
    use HasFactory, SoftDeletes;

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
}
