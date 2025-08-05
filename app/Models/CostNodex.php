<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Deduction; 

class CostNodex extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cost_nodesx';
    protected $primaryKey = 'id';
    public    $incrementing = false;          // PK no autoincremental
    protected $keyType      = 'string';       // PK tipo string


    protected $fillable = [
        'id',
        'concept',
        'value',
        'partner_id',
        'referral_partner',
        'cscheme_id',
    ];

    // ----------------------------------
    //            Relaciones
    // ----------------------------------

    /* ─── belongsTo ─── */
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
    
}

