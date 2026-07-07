<?php

namespace App\Models;

use App\Models\Traits\HasAuditLogs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class UnderwrittenBudgetItem extends Model
{
    use HasFactory, SoftDeletes, HasAuditLogs;

    protected $table = 'underwritten_budget_items';

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'id',
        'budget_id',
        'reinsurer_id',
        'premium_budget',
        'm01','m02','m03','m04','m05','m06',
        'm07','m08','m09','m10','m11','m12',
    ];

    protected $casts = [
        'premium_budget' => 'decimal:2',
        'm01' => 'decimal:2', 'm02' => 'decimal:2', 'm03' => 'decimal:2',
        'm04' => 'decimal:2', 'm05' => 'decimal:2', 'm06' => 'decimal:2',
        'm07' => 'decimal:2', 'm08' => 'decimal:2', 'm09' => 'decimal:2',
        'm10' => 'decimal:2', 'm11' => 'decimal:2', 'm12' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    // ── Relationships ──────────────────────────────────────

    public function budget(): BelongsTo
    {
        return $this->belongsTo(UnderwrittenBudget::class, 'budget_id');
    }

    public function reinsurer(): BelongsTo
    {
        return $this->belongsTo(Reinsurer::class);
    }

    // ── Helpers ────────────────────────────────────────────

    /** Sum of all monthly values. */
    public function getMonthTotalAttribute(): float
    {
        $sum = 0.0;
        foreach (['m01','m02','m03','m04','m05','m06','m07','m08','m09','m10','m11','m12'] as $m) {
            $sum += (float) $this->$m;
        }
        return $sum;
    }

    // ── Audit ──────────────────────────────────────────────

    public function getAuditLabelAttribute(): string
    {
        return "Budget {$this->budget?->year} v{$this->budget?->version} — {$this->reinsurer?->short_name}";
    }
}
