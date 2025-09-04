<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoardDirector extends Model
{
    //
    use HasFactory, SoftDeletes;

    protected $table = 'board_directors';
    protected $fillable = ['board_id', 'director_id'];

    public function director(): BelongsTo
    {
        return $this->belongsTo(Director::class);
    }
}
