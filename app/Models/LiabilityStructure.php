<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LiabilityStructure extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
            'index',
            'coverage_id',
            'cls',
            'limit',
            'limit_desc',
            'sublimit',
            'sublimit_desc',
            'deductible',
            'deductible_desc',
            'business_code'
    ];
    /* ---------------------------------------------------
     |  ➜  Relaciones belongsTo
     ---------------------------------------------------*/
    public function business()
    {
        return $this->belongsTo(Business::class,'business_code', 'business_code');
    }

    public function coverage()
    {
        return $this->belongsTo(Coverage::class, 'coverage_id');
    }


}
