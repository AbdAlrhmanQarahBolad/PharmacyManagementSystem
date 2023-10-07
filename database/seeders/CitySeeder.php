<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       // City::truncate();
        City::create([
            'name_en'=>'Damascus' ,
            'name_ar'=>'دمشق' ,
        ]);
        City::create([
            'name_en'=>'Damascus Countryside' ,
            'name_ar'=>'ريف دمشق' ,
        ]);
        City::create([
            'name_en'=>'Aleppo' ,
            'name_ar'=>'حلب' ,
        ]);
        City::create([
            'name_en'=>'Daraa' ,
            'name_ar'=>'درعا' ,
        ]);
        City::create([
            'name_en'=>'Deir ez-Zor' ,
            'name_ar'=>'دير الزور' ,
        ]);
        City::create([
            'name_en'=>'Hama' ,
            'name_ar'=>'حماة' ,
        ]);
        City::create([
            'name_en'=>'Al-Hasakah' ,
            'name_ar'=>'الحسكة' ,
        ]);
        City::create([
            'name_en'=>'Homs' ,
            'name_ar'=>'حمص' ,
        ]);
        City::create([
            'name_en'=>'Idlib' ,
            'name_ar'=>'إدلب' ,
        ]);
        City::create([
            'name_en'=>'Latakia' ,
            'name_ar'=>'اللاذقية' ,
        ]);
        City::create([
            'name_en'=>'Quneitra' ,
            'name_ar'=>'القنيطرة' ,
        ]);
        City::create([
            'name_en'=>'Ar-Raqqah' ,
            'name_ar'=>'الرقة' ,
        ]);
        City::create([
            'name_en'=>'As-Suwayda' ,
            'name_ar'=>'السويداء' ,
        ]);
        City::create([
            'name_en'=>'Tartus' ,
            'name_ar'=>'طرطوس' ,
        ]);


    }
}
