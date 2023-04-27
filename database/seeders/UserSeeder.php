<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(100)->create();
        
        $user = User::find(31);
        $user->email = 'admin@buckhill.co.uk';
        $user->password = 'admin';
        $user->is_admin = 1;
        $user->save();
    }
}
