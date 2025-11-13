<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DivisiSeeder::class,
            JabatanSeeder::class,
            JenisCutiSeeder::class,
            UserSeeder::class,
            HariLiburSeeder::class,
        ]);
    }
}
