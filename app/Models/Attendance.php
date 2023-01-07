<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendant_id', 'user_card_id', 'attendant_name', 'client_name', 'client_phone', 'user_card_code', 'type'
    ];
}
