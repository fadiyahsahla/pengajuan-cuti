<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jabatan;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        $jabatan = [
            ['nama_jabatan' => 'Operator Produksi', 'level_jabatan' => 1],
            ['nama_jabatan' => 'Operator Mekanik', 'level_jabatan' => 1],
            ['nama_jabatan' => 'Kepala Regu', 'level_jabatan' => 2],
            ['nama_jabatan' => 'Pengawas', 'level_jabatan' => 2],
            ['nama_jabatan' => 'Kepala Bagian Produksi', 'level_jabatan' => 3],
            ['nama_jabatan' => 'Kepala Bagian Mekanik', 'level_jabatan' => 3],
            ['nama_jabatan' => 'Personalia', 'level_jabatan' => 4],
            ['nama_jabatan' => 'HRD', 'level_jabatan' => 5],
            ['nama_jabatan' => 'Admin', 'level_jabatan' => 6],
            ['nama_jabatan' => 'Asisten Personalia', 'level_jabatan' => 4],
        ];

        foreach ($jabatan as $j) {
            Jabatan::create($j);
        }
    }
}
