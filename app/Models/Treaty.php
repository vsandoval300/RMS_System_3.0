<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasAuditLogs;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use App\Models\TreatyDoc;

class Treaty extends Model
{
    use HasFactory, SoftDeletes, HasAuditLogs;

    protected $table = 'treaties';
    protected $primaryKey = 'treaty_code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'treaty_code',
        'reinsurer_id',
        'name',
        'contract_type',
        'description',
    ];

    public function reinsurer(): BelongsTo
    {
        return $this->belongsTo(Reinsurer::class, 'reinsurer_id');
    }

    public function businesses()
    {
        return $this->hasMany(Business::class, 'parent_id', 'treaty_code');
    }

    /** 🔗 Un Treaty tiene muchos documentos */
    public function docs()
    {
        return $this->hasMany(TreatyDoc::class, 'treaty_code', 'treaty_code');
    }

    protected static function booted()
    {
        static::deleting(function (Treaty $treaty) {
            // 👉 Si solo quieres borrar docs cuando sea forceDelete:
            if (method_exists($treaty, 'isForceDeleting') && ! $treaty->isForceDeleting()) {
                return;
            }

            // Borrar también sus TreatyDocs (esto dispara el borrado del archivo en TreatyDoc)
            $treaty->docs()->get()->each->delete();
        });
    }
}
