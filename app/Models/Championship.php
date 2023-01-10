<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Game;

class Championship extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function matchs() {
        return $this->hasMany(Game::class);
    }
}
