<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalCuti extends Model
{
    protected $table = 'approval_cuti';

    protected $fillable = [
        'pengajuan_id',
        'approver_id',
        'level_approval',
        'status_approval',
        'catatan',
        'catatan_reject',
        'tanggal_approval',
        'notified_at',
    ];

    protected $casts = [
        'tanggal_approval' => 'datetime',
        'notified_at' => 'datetime',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanCuti::class, 'pengajuan_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function isPending()
    {
        return $this->status_approval === 'pending';
    }

    public function markAsNotified()
    {
        $this->update(['notified_at' => now()]);
    }
}
