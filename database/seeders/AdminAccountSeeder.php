<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Attendant;

class AdminAccountSeeder extends Seeder
{
    
    public function run()
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'phone' => '99999999999',
            'password' => Hash::make('1234'),
            'active' => true,
            'level' => 'admin',
        ]);

        Attendant::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'phone' => $user->phone,
        ]);
    }
}
