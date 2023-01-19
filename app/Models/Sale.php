<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\UserCard;
use App\Models\Attendant;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendant_id', 'user_card_id', 'name', 'code', 'phone', 'value'
    ];

    public function userCard() {
        return $this->belongsTo(UserCard::class);   
    }

    public function attendant() {
        return $this->belongsTo(Attendant::class);
    }
}
