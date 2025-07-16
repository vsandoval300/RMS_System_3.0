<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BoardMeeting extends Model
{
    //
    use HasFactory, SoftDeletes;
    
    protected $fillable = ['meeting_date', 'description','document_path' ];

}
