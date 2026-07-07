<?php

namespace App\Models;

use App\Models\Traits\HasAuditLogs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class UnderwrittenBudget extends Model
{
    use HasFactory, SoftDeletes, HasAuditLogs;

    protected $table = 'underwritten_budgets';

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'id',
        'year',
        'version',
        'label',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'year'    => 'integer',
        'version' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }

            // Auto-calculate version if not explicitly provided
            if (empty($model->version)) {
                $max = static::withoutGlobalScopes()
                    ->where('year', $model->year)
                    ->max('version');

                $model->version = $max ? $max + 1 : 1;
            }
        });

        // Soft-delete cascade: DB-level cascade only fires on hard DELETE,
        // so we propagate soft deletes/restores via model events.
        static::deleting(function (self $model) {
            $model->items()->each(fn (UnderwrittenBudgetItem $item) => $item->delete());
        });

        static::restoring(function (self $model) {
            $model->items()->withTrashed()->each(fn (UnderwrittenBudgetItem $item) => $item->restore());
        });
    }

    // ── Relationships ──────────────────────────────────────

    public function items(): HasMany
    {
        return $this->hasMany(UnderwrittenBudgetItem::class, 'budget_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Scopes ─────────────────────────────────────────────

    /** Latest version header for a given year. */
    public function scopeLatestVersion($query, int $year)
    {
        $max = static::withoutGlobalScopes()
            ->where('year', $year)
            ->whereNull('deleted_at')
            ->max('version');

        return $query->where('year', $year)->where('version', $max);
    }

    /** All headers for a specific year + version snapshot. */
    public function scopeForVersion($query, int $year, int $version)
    {
        return $query->where('year', $year)->where('version', $version);
    }

    // ── Helpers ────────────────────────────────────────────

    /** Next version number available for a given year. */
    public static function nextVersionForYear(int $year): int
    {
        $max = static::withoutGlobalScopes()
            ->where('year', $year)
            ->max('version');

        return $max ? $max + 1 : 1;
    }

    /** Sum of all item budgets for this version. */
    public function totalBudget(): float
    {
        return (float) $this->items()->sum('premium_budget');
    }

    // ── Audit ──────────────────────────────────────────────

    public function getAuditLabelAttribute(): string
    {
        return "Budget {$this->year} v{$this->version} — {$this->label}";
    }
}
