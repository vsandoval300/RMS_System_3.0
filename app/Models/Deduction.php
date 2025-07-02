<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deduction extends Model
{
    use SoftDeletes;

    /**
     * Nombre de la tabla (opcional; Laravel lo deduciría,
     * pero lo dejo explícito por claridad).
     */
    protected $table = 'deductions';

    /**
     * Campos que se pueden asignar de forma masiva.
     */
    protected $fillable = [
        'concept',
        'description',
    ];

    /**
     * Ejemplo de relación, si en el futuro una deducción
     * pertenece a una factura:
     *
     * public function invoice()
     * {
     *     return $this->belongsTo(Invoice::class);
     * }
     */
}

