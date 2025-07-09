<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CostSchemeNode extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cost_scheme_nodes';

    protected $fillable = [
        'cscheme_id',
        'costnode_id',
        'index',
    ];

    /* ─── belongsTo ─── */
    public function costScheme()
    {
        return $this->belongsTo(CostScheme::class, 'cscheme_id');
    }

    public function costNode()
    {
        return $this->belongsTo(CostNode::class, 'costnode_id');
    }
}


