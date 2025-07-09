<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Reinsurer extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'cns_reinsurer',
        'name',
        'short_name',
        'parent_id',
        'acronym',
        'class',
        'logo',
        'icon',
        'established',
        'manager_id',
        'country_id',
        'reinsurer_type_id',
        'operative_status_id',
    ];

    protected $table = 'reinsurers';

    /* ---------------------------------------------------
     |  ➜  Relaciones belongsTo
     ---------------------------------------------------*/
    public function reinsurer_type(): BelongsTo
    {
        return $this->belongsTo(ReinsurerType::class, 'reinsurer_type_id');
    }

    public function country(): BelongsTo
    {
    return $this->belongsTo(Country::class, 'country_id');
    }

    public function operative_status(): BelongsTo
    {
    return $this->belongsTo(OperativeStatus::class, 'operative_status_id');
    }
    // 👉 Reasegurador Padre
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }    
    public function manager(): BelongsTo
    {
    return $this->belongsTo(Manager::class, 'manager_id');
    }
    
    /* ---------------------------------------------------
     |  ➜  Relaciones hasMany
     ---------------------------------------------------*/
    // 👉 Reaseguradores Hijos
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
    // 👉 Reaseguradores y Documents
    public function documents(): HasMany   // ← el nombre DEBE ser documents()
    {
        return $this->hasMany(ReinsurerDoc::class, 'reinsurer_id');
    }
    // 👉 Reaseguradores y Cuentas Bancarias
    public function reinsurerBankAccounts(): HasMany
    {
        return $this->hasMany(ReinsurerBankAccount::class);
    }
    // 👉 Reaseguradores y Financial Statements
    public function financialStatements()
    {
        return $this->hasMany(ReinsurerFinancialStatement::class);
    }
    // 👉 Reaseguradores y Holdings
    public function reinsurerHoldings(): HasMany
    {
        return $this->hasMany(ReinsurerHolding::class);
    }
    // 👉 Reaseguradores y Boards
    public function reinsurerBoards(): HasMany
    {
        return $this->hasMany(ReinsurerBoard::class);
    }
    public function businesses()
    {
        return $this->hasMany(Business::class);
    }

    /* ---------------------------------------------------
     |  ➜  Relaciones BelongsToMany
     ---------------------------------------------------*/
    public function boards(): BelongsToMany
    {
        return $this->belongsToMany(
                Board::class,
                'reinsurer_boards'
            )
            ->using(ReinsurerBoard::class)
            ->withPivot(['id', 'appt_date'])
            ->withTimestamps();
    }



    




}
