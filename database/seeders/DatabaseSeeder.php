<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Table;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();


        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        User::create([
            'name' => 'Sarah Pelayan',
            'email' => 'pelayan@test.com',
            'password' => bcrypt('password'),
            'role' => 'pelayan',
        ]);

        User::create([
            'name' => 'Budi Kasir',
            'email' => 'kasir@test.com',
            'password' => bcrypt('password'),
            'role' => 'kasir',
        ]);

        // Buat 15 Meja [cite: 3, 6]
        for ($i = 1; $i <= 15; $i++) {
            Table::create(['table_number' => $i, 'status' => 'available']);
        }
    }
}
