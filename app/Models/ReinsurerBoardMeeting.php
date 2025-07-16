<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReinsurerBoardMeeting extends Model
{
    //
    use SoftDeletes;

    protected $table = 'reinsurer_bmeetings'; // ✅ aquí redirigimos la tabla
    protected $fillable = ['board_meeting_id', 'reinsurer_id'];

}

