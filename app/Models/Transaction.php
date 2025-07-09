<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    //

    public function operativeDoc()
    {
        return $this->belongsTo(OperativeDoc::class, 'op_document_id');
    }
}
