<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasAuditLogs;

class Treaty extends Model
{
    use HasFactory, SoftDeletes, HasAuditLogs;

    protected $table = 'treaties';
    protected $primaryKey = 'treaty_code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'treaty_code',
        'name',
        'contract_type',
        'description',
        'document_path',
    ];

    /** 🔗 Relación hacia Business usando parent_id */
    public function businesses()
    {
        return $this->hasMany(Business::class, 'parent_id', 'treaty_code');
    }
}