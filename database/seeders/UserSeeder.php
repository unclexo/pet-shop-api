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
        $users = User::factory(100)->create();

        $user = User::find($users->random()->uuid);
        $user->email = 'admin@buckhill.co.uk';
        $user->password = bcrypt('admin');
        $user->is_admin = 1;
        $user->save();
    }
}
