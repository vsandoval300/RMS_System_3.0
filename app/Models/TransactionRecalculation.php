<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use App\Models\Traits\HasAuditLogs;

class TransactionRecalculation extends Model
{
    use SoftDeletes, HasAuditLogs;

    protected $table      = 'transaction_recalculations';
    protected $primaryKey = 'id';
    public    $incrementing = false;
    protected $keyType      = 'string';

    protected $fillable = [
        'id',
        'transaction_id',
        'recalculation_no',
        'bordereaux_reference',
        'reported_premium',
        'reported_claims',
        'previous_amount',
        'new_amount',
        'evidence_path',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'reported_premium' => 'decimal:2',
        'reported_claims'  => 'decimal:2',
        'previous_amount'  => 'decimal:2',
        'new_amount'       => 'decimal:2',
        'evidence_path'    => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (blank($model->id)) {
                $model->id = (string) Str::uuid();
            }

            if (blank($model->recalculation_no)) {
                $max = self::where('transaction_id', $model->transaction_id)
                    ->withTrashed()
                    ->max('recalculation_no');

                $model->recalculation_no = ($max ?? 0) + 1;
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* --------------------------------------------------
     |  Audit helpers
     --------------------------------------------------*/
    protected function getAuditOwnerModel(): Model
    {
        return $this->transaction ?? $this;
    }

    protected function getAuditLabelIdentifier(): ?string
    {
        $txLabel = $this->transaction?->auditLabel();

        if ($txLabel && $this->recalculation_no) {
            return "{$txLabel} / REC-" . str_pad((string) $this->recalculation_no, 2, '0', STR_PAD_LEFT);
        }

        return $txLabel ?? parent::getAuditLabelIdentifier();
    }
}
