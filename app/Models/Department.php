<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    //
use SoftDeletes;

     protected $fillable = ['name', 'business_unit_id'];

    /* Ejemplo de relación si cada departamento “pertenece” a una unidad de negocio */
    public function businessUnit()
    {
        return $this->belongsTo(BusinessUnit::class);
    }
}
