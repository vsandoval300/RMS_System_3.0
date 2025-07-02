<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceStatus extends Model
{
    use SoftDeletes;

    /**
     * Nombre de la tabla.
     * Laravel lo deduciría (invoice_statuses), pero lo dejamos explícito.
     */
    protected $table = 'invoice_statuses';

    /**
     * Campos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'invoice_status',
    ];

    /**
     * Constantes de ayuda para evitar magia de strings.
     */
    public const PAID     = 'Paid';
    public const UNPAID   = 'Unpaid';
    public const OVERDUE  = 'Overdue';

    /**
     * Cast: el ENUM se guarda como string.
     * (Si usas PHP 8.1+ puedes sustituirlo por un Enum nativo.)
     */
    protected $casts = [
        'invoice_status' => 'string',
        // 'invoice_status' => \App\Enums\InvoiceStatusEnum::class, // ← versión con Enum
    ];
}

