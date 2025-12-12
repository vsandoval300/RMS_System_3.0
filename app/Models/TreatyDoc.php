<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\HasAuditLogs;

class TreatyDoc extends Model
{
    use HasFactory, SoftDeletes, HasAuditLogs;

    protected $table = 'treaty_docs';

    protected $fillable = [
        'index',
        'treaty_code',
        'description',
        'document_path',
    ];

    /** ⛓ Muchos documentos pertenecen a un Treaty */
    public function treaty(): BelongsTo
    {
        // FK = treaty_code en esta tabla, PK = treaty_code en treaties
        return $this->belongsTo(Treaty::class, 'treaty_code', 'treaty_code');
    }

    /** Nombre del archivo (helper) */
    public function getFileNameAttribute(): ?string
    {
        return $this->document_path
            ? basename($this->document_path)
            : null;
    }

    /** Al borrar el registro, borra también el archivo del disco (en forceDelete) */
    protected static function booted()
    {
        static::deleting(function (TreatyDoc $doc) {
            // Si usas SoftDeletes, solo borrar físicamente en forceDelete
            if (method_exists($doc, 'isForceDeleting') && ! $doc->isForceDeleting()) {
                return;
            }

            if ($doc->document_path && Storage::disk('s3')->exists($doc->document_path)) {
                Storage::disk('s3')->delete($doc->document_path);
            }
        });
    }

    /* ──────────────  Métodos para audit registers  ─────────────── */

    /**
     * 👉 Aquí decimos que el "dueño" de este log es el Treaty.
     * El trait HasAuditLogs usará esto para rellenar owner_type / owner_id
     * con el Treaty, y así el modal del Treaty puede ver estos logs.
     */
    protected function getAuditOwnerModel(): ?Model
    {
        return $this->treaty;   // si es null, el trait pondrá owner_type/id en null
    }

    /**
     * Etiqueta amigable para este registro en el historial.
     * (Esto es lo que se muestra en la columna "event" combinada
     * con el prefijo Created/Updated/etc).
     */
    protected function getAuditLabelIdentifier(): ?string
    {
        $parts = [];

        if ($this->treaty_code) {
            $parts[] = 'Treaty ' . $this->treaty_code;
        }

        if ($this->index) {
            $parts[] = 'Doc ' . $this->index;
        }

        if ($this->description) {
            $parts[] = $this->description;
        }

        return ! empty($parts)
            ? implode(' / ', $parts)
            : parent::getAuditLabelIdentifier();
    }

    /**
     * Opcional: cambiar el prefijo del evento.
     * Si lo quieres igual que en los otros módulos:
     */
    protected function getAuditEventLabelPrefix(string $event): string
    {
        return match ($event) {
            'created' => 'Created TreatyDoc',
            'updated' => 'Updated TreatyDoc',
            'deleted' => 'Deleted TreatyDoc',
            default   => parent::getAuditEventLabelPrefix($event),
        };
    }

    /* ────────────────────────────────────────────────────────────── */
}
