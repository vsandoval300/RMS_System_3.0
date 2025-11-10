<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasAuditLogs; 


class LiabilityStructure extends Model
{
    //
    use SoftDeletes, HasAuditLogs;  // 👈 necesario para que tenga deleted_at

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

    protected function getAuditOwnerModel(): Model
    {
        return $this->business ?? $this;
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

    /* protected function getAuditLabelIdentifier(): ?string
    {
        $base = $this->business_code
            ?: $this->business?->business_code;

        if ($base && $this->index) {
            // 01, 02, 03...
            $suffix = str_pad($this->index, 2, '0', STR_PAD_LEFT);

            return "{$base}-{$suffix}";
        }

        // Fallback: usa la PK (id)
        $key = $this->getKey();

        return $key !== null ? (string) $key : null;
    } */


}
