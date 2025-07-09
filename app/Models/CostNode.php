<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CostNode extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cost_nodes';

    protected $fillable = [
        'concept',
        'value',
        'partner_id',
        'referral_partner',
        'reinsurer_id',
    ];

    protected $casts = [
        'value' => 'decimal:2',
    ];

    /* ─── belongsTo ─── */
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function reinsurer()
    {
        return $this->belongsTo(Reinsurer::class);
    }

    /* ─── hasMany & belongsToMany ─── */
    public function schemeNodes()
    {
        return $this->hasMany(CostSchemeNode::class, 'costnode_id');
    }

    public function costSchemes()
    {
        return $this->belongsToMany(
            CostScheme::class,
            'cost_scheme_nodes',
            'costnode_id',
            'cscheme_id'
        )->withTimestamps()->withPivot('id', 'index');
    }
}

