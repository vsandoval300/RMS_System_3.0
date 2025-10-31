<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LineOfBusiness extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'line_of_businesses';

    protected $fillable = [
        'name',
        'description',
        'risk_covered',
    ];

    /* ─── hasMany ─── */
    public function coverages()
    {
        return $this->hasMany(Coverage::class, 'lob_id');
    }
}

