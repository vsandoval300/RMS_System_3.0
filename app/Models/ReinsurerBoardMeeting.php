<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReinsurerBoardMeeting extends Model
{
    //
    protected $table = 'reinsurer_bmeetings'; // ✅ aquí redirigimos la tabla
    protected $fillable = ['board_meeting_id', 'reinsurer_id'];

}

