<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\UserCard;

class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'matchs', 'endtime', 'price', 'name', 'championship', 'round', 'type', 'host_percentage'
    ];

    public function userCards() {
        return $this->hasMany(UserCard::class);
    }
}
