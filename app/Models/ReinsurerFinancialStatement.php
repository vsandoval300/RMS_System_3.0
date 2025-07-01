<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReinsurerFinancialStatement extends Model
{
    //
    protected $table = 'reinsurer_financials';

    protected $fillable = [
        'start_date',
        'end_date',
        'document_path',
        'reinsurer_id',
    ];

    public function reinsurer()
    {
        return $this->belongsTo(Reinsurer::class);
    }
}
