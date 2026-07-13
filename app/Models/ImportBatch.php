<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ImportBatch extends Model
{
    use SoftDeletes;

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'batch_code',
        'imported_by',
        'approved_by',
        'rejected_by',
        'status',
        'source_file_name',
        'notes_importer',
        'notes_reviewer',
        'summary_json',
        'imported_at',
        'reviewed_at',
    ];

    protected $casts = [
        'summary_json' => 'array',
        'imported_at'  => 'datetime',
        'reviewed_at'  => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (ImportBatch $batch) {
            if (empty($batch->id)) {
                $batch->id = (string) Str::uuid();
            }
            if (empty($batch->batch_code)) {
                $year = now()->year;
                $last = static::withTrashed()
                    ->where('batch_code', 'like', "IMP-{$year}-%")
                    ->orderByDesc('batch_code')
                    ->value('batch_code');
                $seq = $last ? ((int) substr($last, -4)) + 1 : 1;
                $batch->batch_code = 'IMP-' . $year . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function importer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'imported_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejecter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function businesses(): HasMany
    {
        return $this->hasMany(Business::class);
    }

    public function costSchemes(): HasMany
    {
        return $this->hasMany(CostScheme::class);
    }

    public function operativeDocs(): HasMany
    {
        return $this->hasMany(OperativeDoc::class);
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    public function totalRecords(): int
    {
        if (! $this->summary_json) {
            return 0;
        }
        return array_sum(array_column($this->summary_json, 'inserted'));
    }

    public function isPending(): bool   { return $this->status === 'pending_review'; }
    public function isApproved(): bool  { return $this->status === 'approved'; }
    public function isRejected(): bool  { return $this->status === 'rejected'; }
}
