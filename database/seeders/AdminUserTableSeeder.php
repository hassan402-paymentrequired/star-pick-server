<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new \App\Models\Admin();
        $user->name = 'Admin';
        $user->email = 'hassan@gmail.com';
        $user->password = Hash::make('password');
        $user->save();
    }
}
