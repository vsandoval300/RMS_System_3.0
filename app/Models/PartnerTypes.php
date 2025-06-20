<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerTypes extends Model
{
    //
    use HasFactory;
    protected $table = 'partner_types'; // ✅ aquí redirigimos la tabla
}
