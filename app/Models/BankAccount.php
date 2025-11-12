<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasAuditLogs;
use App\Models\Currency; 
use App\Models\Bank; 

class BankAccount extends Model
{
    //
    use HasFactory, SoftDeletes, HasAuditLogs;

    protected $table = 'bank_accounts';

    protected $fillable = [
        'beneficiary_acct_name',
        'beneficiary_address',
        'beneficiary_swift',
        'beneficiary_acct_no',
        'ffc_acct_name',
        'ffc_acct_no',
        'ffc_acct_address',
        'status_account',
        'currency_id',
        'bank_id',
        'intermediary_bank',
    ];


    public function currency(): BelongsTo
    {
    return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function bank_inter(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'intermediary_bank');
    }

    /* ─── Metodos para salvar Logs ─── */
    /* ─── Este guarda la etiqueta del campo a manera de identificador ─── */
    protected function getAuditLabelIdentifier(): ?string
    {
        return $this->name
            ?? $this->name . ':'
            ?? parent::getAuditLabelIdentifier();
    }

    protected function transformAuditValue(string $field, $value)
    {
        if ($value === null || $value === '') {
            return $value;
        }

        return match ($field) {
            'currency_id' => Currency::find($value)?->acronym ?? $value,
            'intermediary_bank' => Bank::find($value)?->name ?? $value,
            'bank_id' => Bank::find($value)?->name ?? $value,
            default       => $value,
        };
    }
    

}
