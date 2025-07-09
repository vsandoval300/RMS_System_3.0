<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiabilityStructure extends Model
{
    //

    /* ---------------------------------------------------
     |  ➜  Relaciones belongsTo
     ---------------------------------------------------*/
    public function business()
    {
        return $this->belongsTo(
            Business::class,
            'business_code',   // FK
            'business_code'    // PK en Business
        );
    }

}
