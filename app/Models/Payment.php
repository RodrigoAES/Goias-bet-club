<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\UserCard;
use App\Models\Entry;

class Payment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_card_id', 'entry_id', 'txid', 'location_id', 'price', 'pix_key', 'paid'
    ];

    public function userCard() {
        return $this->belongsTo(UserCard::class);
    }

    public function entry() {
        return $this->belongsTo(Entry::class);
    }
}
