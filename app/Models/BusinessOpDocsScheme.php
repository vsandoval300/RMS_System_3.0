<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessOpDocsScheme extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'businessdoc_schemes';

    protected $fillable = [
        'index',
        'op_document_id',   // FK → operative_docs.id
        'cscheme_id',       // FK → cost_schemes.id
    ];

    /* ─── belongsTo ─── */
    public function operativeDoc()
    {
        return $this->belongsTo(OperativeDoc::class, 'op_document_id');
    }

    public function costScheme()
    {
        return $this->belongsTo(CostScheme::class, 'cscheme_id');
    }
}


