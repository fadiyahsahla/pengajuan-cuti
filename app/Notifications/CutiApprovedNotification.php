<?php

namespace App\Notifications;

use App\Models\PengajuanCuti;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CutiApprovedNotification extends Notification
{
    use Queueable;

    protected $pengajuan;

    public function __construct(PengajuanCuti $pengajuan)
    {
        $this->pengajuan = $pengajuan;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pengajuan Cuti Disetujui')
            ->greeting('Halo ' . $notifiable->nama . ',')
            ->line('Pengajuan cuti Anda telah disetujui!')
            ->line('Jenis Cuti: ' . $this->pengajuan->jenisCuti->nama_jenis)
            ->line('Periode: ' . $this->pengajuan->tanggal_mulai->format('d/m/Y') . ' - ' . $this->pengajuan->tanggal_selesai->format('d/m/Y'))
            ->line('Durasi: ' . $this->pengajuan->durasi . ' hari')
            ->action('Lihat Detail', url('/pengajuan-cuti'))
            ->line('Selamat menikmati cuti Anda!');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'approved',
            'pengajuan_id' => $this->pengajuan->id,
            'jenis_cuti' => $this->pengajuan->jenisCuti->nama_jenis,
            'tanggal_mulai' => $this->pengajuan->tanggal_mulai->format('Y-m-d'),
            'tanggal_selesai' => $this->pengajuan->tanggal_selesai->format('Y-m-d'),
            'durasi' => $this->pengajuan->durasi,
            'message' => 'Pengajuan cuti Anda telah disetujui',
        ];
    }
}
