<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Attendance;

class Attendant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'phone', 'payment_permission', 'doubt_permission', 'validate_permission'
    ];

    public function attendances() {
        return $this->hasMany(Attendance::class);
    }
}
