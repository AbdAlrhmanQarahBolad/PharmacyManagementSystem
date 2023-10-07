<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::create([
            'name_en'=>'Unipharma',
            'name_ar'=>'يونيفارما'
        ]);
        Company::create([
            'name_en'=>'AVENZOR'
            ,'name_ar'=>'ابن زهر'
        ]);
        Company::create([
            'name_en'=>'BiomedPharma'
            ,'name_ar'=>'بيوميد'
        ]);
        Company::create([
            'name_en'=>'DiamondPharma'
            ,'name_ar'=>'دياموند فارما'
        ]);
        Company::create([
            'name_en'=>'THAMECO'
            ,'name_ar'=>'تاميكو'
        ]);
        Company::create([
            'name_en'=>'Hama-Pharma'
            ,'name_ar'=>'حماة فارما'
        ]);
        Company::create([
            'name_en'=>'Nawras'
            ,'name_ar'=>'النورس'
        ]);
        Company::create([
            'name_en'=>'Ultra-Medica'
            ,'name_ar'=>'الترا ميديكا'
        ]);
        Company::create([
            'name_en'=>'Ibn hayyan'
            ,'name_ar'=>'ابن حيان'
        ]);
        Company::create([
            'name_en'=>'Oubari-Pharma'
            ,'name_ar'=>'أوبري فارما'
        ]);
        Company::create([
            'name_en'=>'Vita-Pharma'
            ,'name_ar'=>'فيتا فارما'
        ]);
        Company::create([
            'name_en'=>'Barackat'
            ,'name_ar'=>'بركات'
        ]);
        Company::create([
            'name_en'=>'aphamia'
            ,'name_ar'=>'أفاميا'
        ]);
        Company::create([
            'name_en'=>'bahri'
            ,'name_ar'=>'بحري'
        ]);
        Company::create([
            'name_en'=>'asia'
            ,'name_ar'=>'اسيا'
        ]);
        Company::create([
            'name_en'=>'human'
            ,'name_ar'=>'هيومن'
        ]);
        Company::create([
            'name_en'=>'city-pharma'
            ,'name_ar'=>'سيتي فارما'
        ]);
        Company::create([
            'name_en'=>'sea-pharma'
            ,'name_ar'=>'سي فارما'
        ]);
        Company::create([
            'name_en'=>'ibn-alhaithm'
            ,'name_ar'=>'ابن الهيثم'
        ]);
        Company::create([
            'name_en'=>'Rama-pharma'
            ,'name_ar'=>'راما فارما'
        ]);
    }
}
