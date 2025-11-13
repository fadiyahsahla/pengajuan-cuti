<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'nip' => '999999',
                'username' =>'Admin System',
                'nama' => 'Admin System',
                'email' => 'admin123@gmail.com',
                'password' => Hash::make('999999'), // Password = NIP
                'divisi_id' => 4,
                'jabatan_id' => 9,
                'sisa_cuti' => 12,
              //  'is_first_login' => false, // Admin tidak perlu ganti password
            ],
            [
                'nip' => '100001',
                'username' =>'Budi',
                'nama' => 'Budi ',
                'email' => 'budi123@gmail.com',
                'password' => Hash::make('100001'),
                'divisi_id' => 3,
                'jabatan_id' => 8,
                'sisa_cuti' => 12,
            ],
            [
                'nip' => '100002',
                'username' =>'Siti Nurhaliza',
                'nama' => 'Siti Nurhaliza',
                'email' => 'siti123@gmail.com',
                'password' => Hash::make('100002'),
                'divisi_id' => 3,
                'jabatan_id' => 7,
                'sisa_cuti' => 12,
            ],
            [
                'nip' => '100003',
                'username' =>'Agus',
                'nama' => 'Agus',
                'email' => 'agus123@gmail.com',
                'password' => Hash::make('100003'),
                'divisi_id' => 1,
                'jabatan_id' => 5,
                'sisa_cuti' => 12,
            ],
            [
                'nip' => '100004',
                'username' =>'Joko',
                'nama' => 'Joko',
                'email' => 'joko123@gmail.com',
                'password' => Hash::make('100004'),
                'divisi_id' => 2,
                'jabatan_id' => 6,
                'sisa_cuti' => 12,
            ],
            [
                'nip' => '100005',
                'username' =>'Rudi',
                'nama' => 'Rudi',
                'email' => 'rudi123@gmail.com',
                'password' => Hash::make('100005'),
                'divisi_id' => 1,
                'jabatan_id' => 3,
                'sisa_cuti' => 12,
            ],
            [
                'nip' => '200001',
                'username' =>'Andi',
                'nama' => 'Andi',
                'email' => 'andi123@gmail.com',
                'password' => Hash::make('200001'),
                'divisi_id' => 1,
                'jabatan_id' => 1,
                'sisa_cuti' => 12,
            ],
            [
                'nip' => '200002',
                'username' =>'Tono',
                'nama' => 'Tono',
                'email' => 'tono123@gmail.com',
                'password' => Hash::make('200002'),
                'divisi_id' => 2,
                'jabatan_id' => 2,
                'sisa_cuti' => 12,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
