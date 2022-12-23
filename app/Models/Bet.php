<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_card_id', 'match_id', 'match_src', 'match_round', 'bet', 'home_score', 'away_score'
    ];
}
