<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PengajuanCuti extends Model
{
    protected $table = 'pengajuan_cuti';

    protected $fillable = [
        'user_id',
        'jenis_cuti_id',
        'divisi_id',
        'jabatan_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'alasan',
        'file_pendukung',
        'status',
        'current_level',
        'catatan_revisi',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jenisCuti()
    {
        return $this->belongsTo(JenisCuti::class);
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function approvals()
    {
        return $this->hasMany(ApprovalCuti::class, 'pengajuan_id');
    }

    public function laporan()
    {
        return $this->hasMany(LaporanCuti::class, 'pengajuan_id');
    }

    // Accessor
    public function getDurasiAttribute()
    {
        return $this->tanggal_mulai->diffInDays($this->tanggal_selesai) + 1;
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isDisetujui()
    {
        return $this->status === 'disetujui';
    }

    public function isDitolak()
    {
        return $this->status === 'ditolak';
    }

    public function getCurrentApprover()
    {
        return $this->approvals()
            ->where('level_approval', $this->current_level)
            ->where('status_approval', 'pending')
            ->first();
    }
}
