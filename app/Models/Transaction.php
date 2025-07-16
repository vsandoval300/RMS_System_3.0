<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'index',
        'proportion',
        'exch_rate',
        'due_date',
        'remmitance_code',
        'op_document_id',
        'transaction_type_id',
        'transaction_status_id',
    ];
    
    
    
    public function operativeDoc()
    {
        return $this->belongsTo(OperativeDoc::class, 'op_document_id');
    }
}
