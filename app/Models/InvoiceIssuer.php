<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceIssuer extends Model
{
    use SoftDeletes;

    /**
     * Nombre de la tabla.
     * ───────────────────
     * Laravel deduciría ‘invoice_issuers’, pero lo dejamos explícito
     * por claridad (y por si más adelante cambias el nombre).
     */
    protected $table = 'invoice_issuers';

    /**
     * Campos asignables masivamente.
     */
    protected $fillable = [
        'name',
        'short_name',
        'acronym',
        'country_id',
        'address',
        'bankaccount_id',
    ];

    /**
     * ─────────── Relaciones ───────────
     */

    /** Cada issuer pertenece a un país. */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /** Puede tener asociada una cuenta bancaria (nullable). */
    public function bankAccount()
    {
        // Nota la FK ‘bankaccount_id’, sin guion bajo
        return $this->belongsTo(BankAccount::class, 'bankaccount_id');
    }
}

