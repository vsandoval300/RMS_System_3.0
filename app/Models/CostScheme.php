<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CostScheme extends Model
{
    use HasFactory, SoftDeletes;
     protected $table = 'cost_schemes';
    protected $primaryKey = 'id';
    public    $incrementing = false;          // PK no autoincremental
    protected $keyType      = 'string';       // PK tipo string

   

    protected $fillable = [
        'id',
        'index',
        'share',
        'agreement_type',
    ];

    /* ─── hasMany & belongsToMany ─── */
    public function businessDocSchemes()
    {
        return $this->hasMany(BusinessOpDocsScheme::class, 'cost_scheme_id');
    }

    /** CostNodes relacionados (vía tabla pivote cost_scheme_nodes) */
    public function costNodexes()
    {
        return $this->hasMany(CostNodex::class, 'cscheme_id');
    }

    /* public function costNodexes()
    {
        return $this->hasMany(CostNodex::class);
    } */

}

