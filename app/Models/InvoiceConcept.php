<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceConcept extends Model
{
    use SoftDeletes;

    /**
     * Nombre de la tabla.
     * Laravel lo deduciría bien, pero lo dejamos explícito.
     */
    protected $table = 'invoice_concepts';

    /**
     * Campos asignables masivamente.
     */
    protected $fillable = [
        'type',
        'description',
    ];

    /**
     * Si quieres exponer constantes con los “type” más comunes,
     * declara aquí, p. ej.:
     *
     * public const PREMIUM   = 'Premium';
     * public const COMMISSION = 'Commission';
     */

    /* ─────────── Relaciones opcionales ───────────
     *
     *  public function invoices()
     *  {
     *      return $this->hasMany(Invoice::class);
     *  }
     *
     * Agrega las que necesites según tu dominio.
     */
}
