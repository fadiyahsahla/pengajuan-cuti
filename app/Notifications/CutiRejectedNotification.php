<?php

namespace App\Notifications;

use App\Models\PengajuanCuti;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CutiRejectedNotification extends Notification
{
    use Queueable;

    protected $pengajuan;
    protected $reason;

    public function __construct(PengajuanCuti $pengajuan, $reason)
    {
        $this->pengajuan = $pengajuan;
        $this->reason = $reason;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pengajuan Cuti Ditolak')
            ->greeting('Halo ' . $notifiable->nama . ',')
            ->line('Pengajuan cuti Anda ditolak.')
            ->line('Jenis Cuti: ' . $this->pengajuan->jenisCuti->nama_jenis)
            ->line('Periode: ' . $this->pengajuan->tanggal_mulai->format('d/m/Y') . ' - ' . $this->pengajuan->tanggal_selesai->format('d/m/Y'))
            ->line('Alasan Penolakan: ' . $this->reason)
            ->action('Lihat Detail', url('/pengajuan-cuti'))
            ->line('Anda dapat merevisi dan mengajukan kembali.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'rejected',
            'pengajuan_id' => $this->pengajuan->id,
            'jenis_cuti' => $this->pengajuan->jenisCuti->nama_jenis,
            'tanggal_mulai' => $this->pengajuan->tanggal_mulai->format('Y-m-d'),
            'tanggal_selesai' => $this->pengajuan->tanggal_selesai->format('Y-m-d'),
            'alasan_penolakan' => $this->reason,
            'message' => 'Pengajuan cuti Anda ditolak',
        ];
    }
}
