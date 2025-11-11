<?php

namespace App\Models\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Facades\Auth;

trait HasAuditLogs
{
    public static function bootHasAuditLogs(): void
    {
        static::created(fn ($model) => $model->storeAudit('created'));
        static::updated(fn ($model) => $model->storeAudit('updated'));
        static::deleted(fn ($model) => $model->storeAudit('deleted'));
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    protected function getAuditOwnerModel(): EloquentModel
    {
        return $this;
    }

    /**
     * Identificador que se muestra en el label del evento.
     * Por defecto, la PK del modelo.
     */
    protected function getAuditLabelIdentifier(): ?string
    {
        $key = $this->getKey();

        return $key !== null ? (string) $key : null;
    }

    protected function transformAuditValue(string $field, $value)
    {
        return $value;
    }

    public function storeAudit(string $event): void
    {
        $userId  = Auth::id();
        $changes = [];

        if ($event === 'updated') {
            foreach ($this->getDirty() as $field => $new) {
                if (in_array($field, ['updated_at'])) {
                    continue;
                }

                $old = $this->getOriginal($field);

                $changes[$field] = [
                    'old' => $this->transformAuditValue($field, $old),
                    'new' => $this->transformAuditValue($field, $new),
                ];
            }

            if (empty($changes)) {
                return;
            }
        }

        $target = $this->getAuditOwnerModel();
        $modelName = class_basename($this);
        $identifier = $this->getAuditLabelIdentifier();

        $eventLabel = ucfirst($event) . ' ' . $modelName;
        if ($identifier) {
            $eventLabel .= ' ' . $identifier;
        }

        $target->auditLogs()->create([
            'user_id' => $userId,
            'event'   => $eventLabel,
            'changes' => $changes,
        ]);
    }
}
