<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $fillable = [
        'position',
        'description',
    ];

    // Relación inversa: una posición tiene muchos usuarios
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
