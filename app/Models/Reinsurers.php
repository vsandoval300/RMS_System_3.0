<?php

namespace App\Models;

use App\Models\Manager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reinsurers extends Model



{
    public function reinsurer_type(): BelongsTo
    {
        return $this->belongsTo(ReinsurerType::class, 'reinsurer_type_id');
    }

    public function country(): BelongsTo
    {
    return $this->belongsTo(Countries::class, 'country_id');
    }

    public function operative_status(): BelongsTo
    {
    return $this->belongsTo(OperativeStats::class, 'operative_status_id');
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
    return $this->belongsTo(Managers::class, 'manager_id');
    }


}
