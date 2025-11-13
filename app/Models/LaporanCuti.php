<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanCuti extends Model
{
    protected $table = 'laporan_cuti';

    protected $fillable = [
        'pengajuan_id',
        'periode',
        'file_laporan',
        'dibuat_oleh',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanCuti::class, 'pengajuan_id');
    }

    public function pembuatLaporan()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}
