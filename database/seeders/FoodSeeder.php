<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FoodSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('foods')->insert([
            [
                'name' => 'Ayam Bakar Madu',
                'price' => 30000,
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Es Teh Manis',
                'price' => 5000,
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kopi Hitam',
                'price' => 8000,
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
