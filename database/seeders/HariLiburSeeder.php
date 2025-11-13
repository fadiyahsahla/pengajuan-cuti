<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HariLibur;

class HariLiburSeeder extends Seeder
{
    public function run(): void
    {
        $hariLibur = [
            ['tanggal' => '2025-01-01', 'nama_libur' => 'Tahun Baru Masehi', 'tahun' => 2025],
            ['tanggal' => '2025-03-31', 'nama_libur' => 'Hari Raya Idul Fitri', 'tahun' => 2025],
            ['tanggal' => '2025-04-01', 'nama_libur' => 'Hari Raya Idul Fitri', 'tahun' => 2025],
            ['tanggal' => '2025-05-01', 'nama_libur' => 'Hari Buruh Internasional', 'tahun' => 2025],
            ['tanggal' => '2025-05-29', 'nama_libur' => 'Kenaikan Isa Almasih', 'tahun' => 2025],
            ['tanggal' => '2025-06-01', 'nama_libur' => 'Hari Lahir Pancasila', 'tahun' => 2025],
            ['tanggal' => '2025-08-17', 'nama_libur' => 'Hari Kemerdekaan RI', 'tahun' => 2025],
            ['tanggal' => '2025-12-25', 'nama_libur' => 'Hari Raya Natal', 'tahun' => 2025],
        ];

        foreach ($hariLibur as $hl) {
            HariLibur::create($hl);
        }
    }
}
