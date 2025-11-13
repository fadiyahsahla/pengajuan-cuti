<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'nip',
        'username',
        'nama',
        'email',
        'password',
        'divisi_id',
        'jabatan_id',
        'sisa_cuti',
        'is_active',
        //'is_first_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
           // 'is_first_login' => 'boolean',
        ];
    }

    // Relationships
    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function pengajuanCuti()
    {
        return $this->hasMany(PengajuanCuti::class);
    }

    public function approvals()
    {
        return $this->hasMany(ApprovalCuti::class, 'approver_id');
    }

    // Helper methods
    public function isAdmin()
    {
        return $this->jabatan_id === 9;
    }

    public function isHRD()
    {
        return $this->jabatan_id === 8;
    }

    public function isPersonalia()
    {
        return $this->jabatan_id === 7;
    }

    public function canApproveCuti()
    {
        return $this->jabatan && $this->jabatan->level_jabatan >= 2;
    }

   /* public function needsPasswordChange()
    {
        return $this->is_first_login;
    }*/
}
