<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Area::truncate();
        Area::create([
            'name_en'=>'Al-Mhajren' ,
            'name_ar'=>'المهاجرين' ,
            'city_id'=>1 ,
        ]);
        Area::create([
            'name_en'=>'Al-Mazeh' ,
            'name_ar'=>'المزة' ,
            'city_id'=>1 ,
        ]);
        Area::create([
            'name_en'=>'Alshagour' ,
            'name_ar'=>'الشاغور' ,
            'city_id'=>1 ,
        ]);
        Area::create([
            'name_en'=>'Sahnaia' ,
            'name_ar'=>'صحنايا' ,
            'city_id'=>1 ,
        ]);
        Area::create([
            'name_en'=>'Al-Amen-street' ,
            'name_ar'=>'شارع الأمين' ,
            'city_id'=>1 ,
        ]);
        Area::create([
            'name_en'=>'Al-Hamraa' ,
            'name_ar'=>'الحمرا' ,
            'city_id'=>1 ,
        ]);
        Area::create([
            'name_en'=>'Al-Medan' ,
            'name_ar'=>'الميدان' ,
            'city_id'=>1 ,
        ]);
        Area::create([
            'name_en'=>'AL-Jazmatea' ,
            'name_ar'=>'الجزماتية' ,
            'city_id'=>1 ,
        ]);
        Area::create([
            'name_en'=>'Al-Shalaan' ,
            'name_ar'=>'الشعلان' ,
            'city_id'=>1 ,
        ]);
        /*
        Area::create([
            'name'=>'Latakia' ,
        ]);
        Area::create([
            'name'=>'Quneitra' ,
        ]);
        Area::create([
            'name'=>'Ar-Raqqah' ,
        ]);
        Area::create([
            'name'=>'As-Suwayda' ,
        ]);
        Area::create([
            'name'=>'Tartus' ,
        ]);
*/
    }
}
