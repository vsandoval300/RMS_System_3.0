<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasAuditLogs;

class Currency extends Model
{
    //
     use HasFactory, SoftDeletes, HasAuditLogs;

    protected $fillable = [
        'name',
        'acronym',
    ];

    /* ---------------------------------------------------
     |  ➜  Relaciones hasMany
     ---------------------------------------------------*/
    public function bankaccounts(): HasMany
    {
        return $this->hasMany(bankAccount::class);
    }

    public function businesses()
    {
        return $this->hasMany(Business::class);
    }

    /* ─── Metodos para salvar Logs ─── */
    /* ─── Este guarda la etiqueta del campo a manera de identificador ─── */
    protected function getAuditLabelIdentifier(): ?string
    {
        return $this->name
            ?? $this->name . ':'
            ?? parent::getAuditLabelIdentifier();
    }
    

}
