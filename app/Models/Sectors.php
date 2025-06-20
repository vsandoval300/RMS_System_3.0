<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sectors extends Model
{
    //
    use HasFactory;
    protected $table = 'industries'; // ✅ aquí redirigimos la tabla


public function companie(): BelongsTo
    {
        return $this->belongsTo(Companies::class);
    }

}
