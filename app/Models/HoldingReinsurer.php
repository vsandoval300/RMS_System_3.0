<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HoldingReinsurer extends Model
{
    
    use SoftDeletes;
    /**
     * Si tu tabla se llama **holding_reinsurers** (plural, snake_case)
     * Laravel la deduce; si es singular (holding_reinsurer) o un nombre
     * distinto, destapa la propiedad $table:
     */
    // protected $table = 'holding_reinsurer';

    /**  
     * Si la tabla NO tiene clave primaria auto-incremental (caso típico
     * de pivots con clave compuesta), desactíalo:
     */
    public $incrementing = false;
    protected $primaryKey = null;

    /**  
     * Si no hay `created_at/updated_at` en la tabla, desactiva timestamps:
     */
    public $timestamps = false;      // cámbialo si tu tabla SÍ los tiene

    protected $fillable = [
        'holding_id',
        'reinsurer_id',
        'percentage',
    ];

    /* Relación opcional */
    public function holding()
    {
        return $this->belongsTo(Holding::class);
    }

    public function reinsurer()
    {
        return $this->belongsTo(Reinsurer::class);
    }
}

