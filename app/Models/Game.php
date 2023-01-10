<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $table = 'matchs';

    protected $fillable = [
        'championship_id', 'group', 'datetime', 'finished', 
        'home_score', 'home_name', 'home_flag',
        'away_score', 'away_name', 'away_flag'
    ];
}
