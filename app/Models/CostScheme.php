<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasAuditLogs;

class CostScheme extends Model
{
    use HasFactory, SoftDeletes, HasAuditLogs;
     protected $table = 'cost_schemes';
    protected $primaryKey = 'id';
    public    $incrementing = false;          // PK no autoincremental
    protected $keyType      = 'string';       // PK tipo string

   

    protected $fillable = [
        'id',
        'index',
        'share',
        'agreement_type',
        'description',

    ];

    /* ─── hasMany & belongsToMany ─── */
    /* public function businessDocSchemes()
    {
        return $this->hasMany(BusinessOpDocsScheme::class, 'cost_scheme_id');
    } */

    public function businessDocSchemes()
    {
        // FK real en la tabla businessdoc_schemes = cscheme_id
        return $this->hasMany(BusinessOpDocsScheme::class, 'cscheme_id', 'id');
    }

    /** CostNodes relacionados (vía tabla pivote cost_scheme_nodes) */
    public function costNodexes()
    {
        return $this->hasMany(CostNodex::class, 'cscheme_id', 'id')
            ->orderBy('index'); // 👈 aquí
    }


    protected function getAuditOwnerModel(): Model
    {
        return $this->operativeDoc?->business
            ?? $this->operativeDoc
            ?? $this;
    }

    

    /* ──────────────────────────────────────────────────────────────
     |  Cascada SoftDelete / Restore / ForceDelete a los hijos
     ──────────────────────────────────────────────────────────────*/
    protected static function booted(): void
    {
        static::deleting(function (CostScheme $scheme) {
            if ($scheme->isForceDeleting()) {
                $scheme->costNodexes()
                    ->withTrashed()
                    ->forceDelete();
                $scheme->businessDocSchemes()
                    ->withTrashed()
                    ->forceDelete();
            } else {
                $scheme->costNodexes()
                    ->get()
                    ->each
                    ->delete();
                $scheme->businessDocSchemes()
                    ->get()
                    ->each
                    ->delete();
            }
        });

        static::restoring(function (CostScheme $scheme) {
            $scheme->costNodexes()
                ->onlyTrashed()
                ->restore();
            $scheme->businessDocSchemes()
                ->onlyTrashed()
                ->restore();
        });


    }


    protected function getAuditLabelIdentifier(): ?string
    {
        return $this->name
            ?? $this->name . ':'
            ?? parent::getAuditLabelIdentifier();
    }

    
}

