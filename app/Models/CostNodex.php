<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Deduction; 
use App\Models\Traits\HasAuditLogs;

class CostNodex extends Model
{
    use HasFactory, SoftDeletes, HasAuditLogs;

    protected $table = 'cost_nodesx';
    protected $primaryKey = 'id';
    public    $incrementing = false;          // PK no autoincremental
    protected $keyType      = 'string';       // PK tipo string


    protected $fillable = [
        'id',
        'concept',
        'value',
        'partner_source_id',
        'partner_destination_id', // nuevo campo
        'referral_partner',
        'cscheme_id',
    ];

    // ----------------------------------
    //            Relaciones
    // ----------------------------------

    /* ─── belongsTo ─── */
    // 🔁 Partner origen
    public function partnerSource()
    {
        return $this->belongsTo(Partner::class, 'partner_source_id');
    }

    // 🔁 Partner destino
    public function partnerDestination()
    {
        return $this->belongsTo(Partner::class, 'partner_destination_id');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
    /** Relación con esquema de costos */
    public function costSchemes()
    {
        return $this->belongsTo(CostScheme::class, 'cscheme_id');
    }
    /** Relación con los tipos de deducciones */
    public function deduction()
    {
        return $this->belongsTo(Deduction::class, 'concept');
    }

    public function costScheme()
    {
        return $this->belongsTo(CostScheme::class, 'cscheme_id');
    }
    
    // 🔑 Donde se guardan los logs del hijo: en el padre
    protected function getAuditOwnerModel(): Model
    {
        // Si existe padre, guárdalo ahí; si no, en el propio hijo (fallback)
        return $this->costScheme ?? $this;
    }

    // (Opcional) etiqueta legible en el historial
    protected function getAuditLabelIdentifier(): ?string
    {
        $concept = $this->deduction?->concept;
        return $concept ? "{$this->id} · {$concept}" : $this->id;
    }

    protected function transformAuditValue(string $field, $value)
    {
        if ($value === null || $value === '') {
            return $value;
        }

        return match ($field) {
            'concept' => Deduction::find($value)?->concept ?? $value,
            'partner_source_id'  => Partner::find($value)?->name ?? $value,
            'partner_destination_id'  => Partner::find($value)?->name ?? $value,
            default       => $value,
        };
    }


}

