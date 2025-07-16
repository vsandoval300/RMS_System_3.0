<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientIndustry extends Model
{
    //
    use HasFactory, SoftDeletes;

    protected $table = 'client_industries';

    protected $fillable = [
        'client_id',
        'industry_id',
        // 'extra_column',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }
}
