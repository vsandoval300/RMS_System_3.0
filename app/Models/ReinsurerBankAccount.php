<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasAuditLogs;

class ReinsurerBankAccount extends Model
{
    //
    use HasFactory, SoftDeletes, HasAuditLogs;

    protected $table = 'reinsurer_bankaccounts';

    protected $fillable = [
        'reinsurer_id',
        'bank_account_id',
        // 'extra_column',
    ];

    public function reinsurer(): BelongsTo
    {
        return $this->belongsTo(Reinsurer::class, 'reinsurer_id');
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    /* ─── Metodos para salvar Logs ─── */
    /* ─── Este guarda la etiqueta del campo a manera de identificador ─── */
    protected function getAuditOwnerModel(): Model
    {
        return $this->reinsurer ?? $this;
    }

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
            'bank_account_id' => BankAccount::find($value)?->ffc_acct_no ?? $value,
            default       => $value,
        };
    }
}
