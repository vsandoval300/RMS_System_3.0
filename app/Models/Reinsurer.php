<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    // 👉 Reaseguradores Hijos
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function manager(): BelongsTo
    {
    return $this->belongsTo(Manager::class, 'manager_id');
    }


}
