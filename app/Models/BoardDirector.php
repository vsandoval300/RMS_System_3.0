<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BoardDirector extends Model
{
    //
    use HasFactory, SoftDeletes;

    protected $fillable = ['board_id', 'director_id'];
}
