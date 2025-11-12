<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\Coverage; 
use App\Models\Company; 
use App\Models\Traits\HasAuditLogs;

class BusinessOpDocsInsured extends Model
{
    use HasFactory, SoftDeletes, HasAuditLogs;

    protected $table = 'businessdoc_insureds';
    protected $primaryKey = 'id';
    public    $incrementing = false;          // PK no autoincremental
    protected $keyType      = 'string';       // PK tipo string

    protected $fillable = [
        'id',
        'op_document_id',   // FK → operative_docs.id
        'company_id',       // FK → companies.id
        'coverage_id',      // FK → coverages.id
        'premium',
    ];

    protected $casts = [
        'premium' => 'decimal:2',
    ];

    /* ─── belongsTo ─── */
    public function operativeDoc()
    {
        return $this->belongsTo(OperativeDoc::class, 'op_document_id');
    }

    /* ──────────────  Metods for audit registers  ──────────────── */
    protected function getAuditOwnerModel(): Model
    {
        return $this->operativeDoc?->business
            ?? $this->operativeDoc
            ?? $this;
    }

    Protected function getAuditLabelIdentifier(): ?string
    {
        $docId    = $this->operativeDoc?->id;
        $company  = $this->company?->name;
        $coverage = $this->coverage?->name;

        if (! $docId) {
            // fallback al comportamiento genérico del trait
            return parent::getAuditLabelIdentifier();
        }

        // Armamos partes legibles
        $parts = [$docId, 'INS'];

        if ($company) {
            $parts[] = Str::limit($company, 50, '');   // acortar un poco
        }

        if ($coverage) {
            $parts[] = Str::limit($coverage, 20, '');
        }

        return implode('-', $parts);
    }

    protected function transformAuditValue(string $field, $value)
    {
        if ($value === null || $value === '') {
            return $value;
        }

        return match ($field) {
            'company_id' => Company::find($value)?->name ?? $value,
            'coverage_id' => Coverage::find($value)?->name ?? $value,
            default       => $value,
        };
    }
    /* ────────────────────────────────────────────────────────────── */

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function coverage()
    {
        return $this->belongsTo(Coverage::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'op_document_id', 'op_document_id');
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

}

