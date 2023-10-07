<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InitAuthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
//         INSERT INTO `oauth_clients` (`id`, `user_id`, `name`, `secret`, `provider`, `redirect`, `personal_access_client`, `password_client`, `revoked`, `created_at`, `updated_at`) VALUES
// (1, NULL, 'Laravel Personal Access Client', 'DdOC7UVuMUp64spcYdENgHtKfl19NmsQ7551tfiF', NULL, 'http://localhost', 1, 0, 0, '2023-05-10 15:19:50', '2023-05-10 15:19:50'),
// (2, NULL, 'Laravel Password Grant Client', 'Vq3ibEGoOR4Nwl2O2Euge5Sxn56xh6ycZeqWxl9R', 'users', 'http://localhost', 0, 1, 0, '2023-05-10 15:19:50', '2023-05-10 15:19:50');
        DB::table('oauth_clients')->insert([
            'user_id' => NULL,
            'name' => 'Laravel Password Grant Client',
            'secret' => 'DdOC7UVuMUp64spcYdENgHtKfl19NmsQ7551tfiF',
            'provider' => NULL,
            'redirect' => 'http://localhost',
            'personal_access_client' =>1,
            'password_client' => 0,
            'revoked' => 0,
            'created_at' => '2023-05-10 15:19:50',
            'updated_at' => '2023-05-10 15:19:50',
        ]);
        DB::table('oauth_clients')->insert([
            'user_id' => NULL,
            'name' => 'Laravel Password Grant Client',
            'secret' => 'Vq3ibEGoOR4Nwl2O2Euge5Sxn56xh6ycZeqWxl9R',
            'provider' => 'users',
            'redirect' => 'http://localhost',
            'personal_access_client' =>0 ,
            'password_client' => 1,
            'revoked' => 0,
            'created_at' => '2023-05-10 15:19:50',
            'updated_at' => '2023-05-10 15:19:50',
        ]);
    }
}
