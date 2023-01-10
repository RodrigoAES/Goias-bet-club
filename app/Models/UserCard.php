<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Bet;
use App\Models\Card;

class UserCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'phone', 'card_id', 'code'
    ];

    public function card() {
        return $this->belongsTo(Card::class);
    }

    public function bets() {
        return $this->hasMany(Bet::class);
    }
}
