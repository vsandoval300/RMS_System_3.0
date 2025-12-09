<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasAuditLogs;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Treaty extends Model
{
    use HasFactory, SoftDeletes, HasAuditLogs;

    protected $table = 'treaties';
    protected $primaryKey = 'treaty_code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'treaty_code',
        'index',
        'reinsurer_id',
        'name',
        'contract_type',
        'description',
        'document_path',
    ];


    /** 🔗 Treaty → Reinsurer (muchos a uno) */
    public function reinsurer(): BelongsTo
    {
        return $this->belongsTo(Reinsurer::class, 'reinsurer_id');
    }


    /** 🔗 Relación hacia Business usando parent_id */
    public function businesses()
    {
        return $this->hasMany(Business::class, 'parent_id', 'treaty_code');
    }

    protected static function booted()
    {
        static::deleting(function (Treaty $treaty) {
            // Si quieres borrar SOLO cuando sea forceDelete (por SoftDeletes)
            if (method_exists($treaty, 'isForceDeleting') && ! $treaty->isForceDeleting()) {
                return;
            }

            if ($treaty->document_path && Storage::disk('s3')->exists($treaty->document_path)) {
                Storage::disk('s3')->delete($treaty->document_path);
            }
        });
    }
}