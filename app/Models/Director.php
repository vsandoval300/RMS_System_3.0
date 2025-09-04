<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Occupation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Director extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'name','surname','gender','email','phone','address',
        'occupation','image','country_id',
    ];

    public function boards(): BelongsToMany
    {
        return $this->belongsToMany(Board::class, 'board_directors')
                    ->withTimestamps();
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->name} {$this->surname}");
    }

     public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
    
                           
}
