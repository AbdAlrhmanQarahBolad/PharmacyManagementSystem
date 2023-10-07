<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Medicine;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            CitySeeder::class,
            CompanySeeder::class,
            InitAuthSeeder::class,
            RoleSeeder::class,
            AreaSeeder::class,
            LocationSeeder::class,
            UserSeeder::class,
            MedicineSeeder::class,
            WeekDaySeeder::class
        ]);
    }
}
