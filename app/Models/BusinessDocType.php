<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasAuditLogs;

class BusinessDocType extends Model
{
    //
    use HasFactory, SoftDeletes, HasAuditLogs;

    protected $fillable = [
        'name',
        'description',
    ];

    protected $table = 'business_doc_types'; // ✅ aquí redirigimos la tabla

    public function operativeDocs()
    {
        return $this->hasMany(OperativeDoc::class, 'operative_doc_type_id');
    }

    protected function getAuditLabelIdentifier(): ?string
    {
        return $this->name
            ?? $this->name . ':'
            ?? parent::getAuditLabelIdentifier();
    }
}
