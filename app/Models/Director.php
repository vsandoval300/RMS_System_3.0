<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Director extends Model
{
    //
    protected $fillable = ['name', 'surname', 'gender', 'email', 'phone', 'address', 'accupation', 'image', 'country_id'];
}
