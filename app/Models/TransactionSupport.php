<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use App\Models\Traits\HasAuditLogs;

class TransactionSupport extends Model
{
    use SoftDeletes, HasAuditLogs;

    protected $table = 'transactions_supports';

    protected $primaryKey = 'id';
    public $incrementing = false;

    // ✅ En Eloquent el uuid se maneja como string
    protected $keyType = 'string';

    /**
     * ✅ Esto hace que si cambia un support,
     * se actualice updated_at del Transaction padre.
     */
    protected $touches = ['transaction'];

    protected $fillable = [
        'id',
        'transaction_id',
        'description',
        'support_path',
    ];

    protected $casts = [
        'id' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Auto-generar UUID
     */
    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (blank($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    /* --------------------------------------------------
     |  Relations
     --------------------------------------------------*/
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    /* ─────────────────────────────────────────────
     | Audit helpers (clave para "Audit info")
     ───────────────────────────────────────────── */

    /**
     * ✅ Hace que los cambios del SUPPORT se registren
     * bajo el Transaction padre (owner = Transaction).
     */
    protected function getAuditOwnerModel(): Model
    {
        return $this->transaction ?? $this;
    }

    /**
     * ✅ Identificador humano (opcional pero recomendado)
     * para que en Audit Info se entienda qué cambió.
     */
    protected function getAuditLabelIdentifier(): ?string
    {
         $txLabel = $this->transaction?->auditLabel();

        $desc = trim((string) ($this->description ?? ''));
        if ($desc !== '') {
            $desc = Str::limit($desc, 28);
        } else {
            $desc = basename((string) ($this->support_path ?? ''));
            $desc = $desc !== '' ? Str::limit($desc, 28) : null;
        }

        if ($txLabel && $desc) {
            return "{$txLabel} / SUPPORT – {$desc}";
        }

        return $txLabel ?? parent::getAuditLabelIdentifier();
    }

}

