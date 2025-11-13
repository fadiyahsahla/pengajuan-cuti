<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Divisi;

class DivisiSeeder extends Seeder
{
    public function run(): void
    {
        $divisi = [
            ['nama_divisi' => 'Produksi'],
            ['nama_divisi' => 'Mekanik'],
            ['nama_divisi' => 'HRD'],
            ['nama_divisi' => 'Admin'],
        ];

        foreach ($divisi as $d) {
            Divisi::create($d);
        }
    }
}
