<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisCuti extends Model
{
    protected $table = 'jenis_cuti';

    protected $fillable = [
        'nama_jenis',
        'keterangan',
        'min_hari_pengajuan',
        'perlu_dokumen',
        'prioritas',
    ];

    protected $casts = [
        'perlu_dokumen' => 'boolean',
    ];

    public function pengajuanCuti()
    {
        return $this->hasMany(PengajuanCuti::class);
    }

    public function isCepat()
    {
        return $this->prioritas === 'cepat';
    }
}
