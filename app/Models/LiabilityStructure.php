<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class LiabilityStructure extends Model
{
    //

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


    protected static function booted(): void
    {
        // Paso 1: Asignar automáticamente el índice al crear
        static::creating(function ($model) {
            $maxIndex = self::where('business_code', $model->business_code)->max('index');
            $model->index = $maxIndex ? $maxIndex + 1 : 1;
        });

        // Paso 2: Reordenar índices al eliminar
        static::deleted(function ($model) {
            self::where('business_code', $model->business_code)
                ->orderBy('index')
                ->get()
                ->values()
                ->each(function ($record, $key) {
                    $record->update(['index' => $key + 1]);
                });
        });
    }


}
