<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CostScheme extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cost_schemes';

    protected $fillable = [
        'index',
        'share',
        'agreement_type',
    ];

    /* ─── hasMany & belongsToMany ─── */
    public function businessDocSchemes()
    {
        return $this->hasMany(BusinessOpDocsScheme::class, 'cscheme_id');
    }

    public function schemeNodes()
    {
        return $this->hasMany(CostSchemeNode::class, 'cscheme_id');
    }

    /** CostNodes relacionados (vía tabla pivote cost_scheme_nodes) */
    public function costNodes()
    {
        return $this->belongsToMany(
            CostNode::class,
            'cost_scheme_nodes',
            'cscheme_id',     // FK a este modelo en la pivote
            'costnode_id'     // FK al otro modelo
        )->withTimestamps()->withPivot('id', 'index');
    }
}

