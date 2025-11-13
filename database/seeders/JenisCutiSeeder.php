<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisCuti;

class JenisCutiSeeder extends Seeder
{
    public function run(): void
    {
        $jenisCuti = [
            [
                'nama_jenis' => 'Cuti Tahunan',
                'keterangan' => 'Cuti tahunan regular',
                'min_hari_pengajuan' => 7,
                'perlu_dokumen' => false,
                'prioritas' => 'normal',
            ],
            [
                'nama_jenis' => 'Cuti Sakit',
                'keterangan' => 'Cuti karena sakit dengan surat dokter',
                'min_hari_pengajuan' => 0,
                'perlu_dokumen' => true,
                'prioritas' => 'cepat',
            ],
            [
                'nama_jenis' => 'Cuti Darurat',
                'keterangan' => 'Cuti mendadak untuk keperluan mendesak',
                'min_hari_pengajuan' => 0,
                'perlu_dokumen' => false,
                'prioritas' => 'cepat',
            ],
            [
                'nama_jenis' => 'Cuti Menikah',
                'keterangan' => 'Cuti untuk menikah',
                'min_hari_pengajuan' => 14,
                'perlu_dokumen' => true,
                'prioritas' => 'normal',
            ],
            [
                'nama_jenis' => 'Cuti Keluarga',
                'keterangan' => 'Cuti karena urusan keluarga',
                'min_hari_pengajuan' => 7,
                'perlu_dokumen' => false,
                'prioritas' => 'normal',
            ],
        ];

        foreach ($jenisCuti as $jc) {
            JenisCuti::create($jc);
        }
    }
}
