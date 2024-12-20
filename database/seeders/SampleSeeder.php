<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert data into the users table
        DB::table('users')->insert([
            [
                'name' => 'Alice',
                'email' => 'alice@example.com',
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bob',
                'email' => 'bob@example.com',
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Insert data into the devices table
        DB::table('devices')->insert([
            [
                'name' => 'Android Phone',
                'model' => 'Model X',
                'device_unique_id' => 'ABC123',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Laptop',
                'model' => 'Model Y',
                'device_unique_id' => 'DEF456',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Insert data into the access table
        DB::table('access')->insert([
            [
                'user_id' => 1, // Alice
                'device_id' => 1, // Alice
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1, // Bob
                'device_id' => 2, // Bob
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

