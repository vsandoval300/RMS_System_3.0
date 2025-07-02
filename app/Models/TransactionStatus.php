<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionStatus extends Model
{
    use SoftDeletes;      // porque tu tabla tiene softDeletes()

    /**
     * Nombre explícito de la tabla (opcional).
     * Laravel lo deduce bien (‘transaction_statuses’), pero lo dejamos
     * escrito por claridad y por si cambias el nombre en el futuro.
     */
    protected $table = 'transaction_statuses';

    /**
     * Campos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'transaction_status',
    ];

    /**
     * Constantes de ayuda para evitar “magia de strings”.
     */
    public const PENDING     = 'Pending';
    public const IN_PROCESS  = 'In process';
    public const COMPLETED   = 'Completed';

    /**
     * Cast del campo. Al ser un ENUM que realmente se guarda
     * como VARCHAR, bastaría con ‘string’; pero si usas PHP 8.1+
     * puedes crear un Enum nativo y castear a ese Enum.
     */
    protected $casts = [
        'transaction_status' => 'string',
        // 'transaction_status' => TransactionStatusEnum::class, // Versión con Enum nativo
    ];
}
