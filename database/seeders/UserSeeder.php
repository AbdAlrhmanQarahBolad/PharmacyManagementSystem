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
        User::create([
            'name'=>'omar' ,
            'email'=>'omar@gmail.com' ,
            'password'=>'12345678',
            'admin_level'=>2,
        ]);
    }
}
