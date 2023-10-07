<?php

namespace Database\Seeders;

use App\Models\WeekDay;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WeekDaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WeekDay::create([
            'name'=>'Sun' ,
        ]);
        WeekDay::create([
            'name'=>'Mon' ,
        ]);
        WeekDay::create([
            'name'=>'Tue' ,
        ]);
        WeekDay::create([
            'name'=>'Wed' ,
        ]);
        WeekDay::create([
            'name'=>'Thu' ,
        ]);
        WeekDay::create([
            'name'=>'Fri' ,
        ]);
        WeekDay::create([
            'name'=>'Sat' ,
        ]);
    }
}
