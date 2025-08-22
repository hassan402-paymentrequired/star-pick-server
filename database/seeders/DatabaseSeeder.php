<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        // User::factory()->create([
        //     'username' => 'Test User',
        //     'email' => 'test@example.com',
        //     'phone' => '1234567890',
        //     'password' => Hash::make('password'),
        // ])->wallet()->create();

        Admin::updateOrCreate([
            'email' => 'admin@admin.com',
        ], [
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'phone' => '1234567890',
            'password' => Hash::make('password'),
        ]);

        $this->call([PeerTableSeeder::class, 
            BankSeeder::class,
            // UserBankAccountSeeder::class,
            // TransactionSeeder::class,
        ]);
    }
}
