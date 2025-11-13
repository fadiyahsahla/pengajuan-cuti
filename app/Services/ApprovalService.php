<?php

namespace App\Services;

use App\Models\PengajuanCuti;
use App\Models\ApprovalCuti;
use App\Helpers\ApprovalHelper;
use App\Notifications\CutiApprovedNotification;
use App\Notifications\CutiRejectedNotification;
use App\Notifications\CutiNeedApprovalNotification;
use Illuminate\Support\Facades\DB;

class ApprovalService
{
    public function createApprovalFlow(PengajuanCuti $pengajuan)
    {
        $approvers = ApprovalHelper::getApprovers($pengajuan);

        foreach ($approvers as $approver) {
            ApprovalCuti::create([
                'pengajuan_id' => $pengajuan->id,
                'approver_id' => $approver['approver_id'],
                'level_approval' => $approver['level'],
                'status_approval' => 'pending',
            ]);
        }

        $this->notifyCurrentApprover($pengajuan);
    }

    public function approve(PengajuanCuti $pengajuan, $approverId, $catatan = null)
    {
        DB::beginTransaction();
        try {
            $approval = ApprovalCuti::where('pengajuan_id', $pengajuan->id)
                ->where('approver_id', $approverId)
                ->where('level_approval', $pengajuan->current_level)
                ->firstOrFail();

            $approval->update([
                'status_approval' => 'disetujui',
                'catatan' => $catatan,
                'tanggal_approval' => now(),
            ]);

            $nextLevel = ApprovalHelper::getNextLevel($pengajuan->current_level, $pengajuan->jabatan_id);

            if ($nextLevel) {
                $pengajuan->update(['current_level' => $nextLevel]);
                $this->notifyCurrentApprover($pengajuan);
            } else {
                $pengajuan->update(['status' => 'disetujui']);
                $this->deductCutiQuota($pengajuan);
                $pengajuan->user->notify(new CutiApprovedNotification($pengajuan));
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function reject(PengajuanCuti $pengajuan, $approverId, $catatanReject)
    {
        DB::beginTransaction();
        try {
            $approval = ApprovalCuti::where('pengajuan_id', $pengajuan->id)
                ->where('approver_id', $approverId)
                ->where('level_approval', $pengajuan->current_level)
                ->firstOrFail();

            $approval->update([
                'status_approval' => 'ditolak',
                'catatan_reject' => $catatanReject,
                'tanggal_approval' => now(),
            ]);

            $pengajuan->update(['status' => 'ditolak']);
            $pengajuan->user->notify(new CutiRejectedNotification($pengajuan, $catatanReject));

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function revise(PengajuanCuti $pengajuan, array $data)
    {
        DB::beginTransaction();
        try {
            $oldData = [
                'tanggal_mulai' => $pengajuan->tanggal_mulai->format('Y-m-d'),
                'tanggal_selesai' => $pengajuan->tanggal_selesai->format('Y-m-d'),
                'alasan' => $pengajuan->alasan,
            ];

            $revisiNote = "Revisi pada " . now()->format('d/m/Y H:i') . "\n";
            $revisiNote .= "Sebelum: " . json_encode($oldData, JSON_UNESCAPED_UNICODE) . "\n";
            $revisiNote .= "Sesudah: " . json_encode($data, JSON_UNESCAPED_UNICODE) . "\n";

            if ($pengajuan->catatan_revisi) {
                $revisiNote = $pengajuan->catatan_revisi . "\n---\n" . $revisiNote;
            }

            $pengajuan->update([
                'tanggal_mulai' => $data['tanggal_mulai'],
                'tanggal_selesai' => $data['tanggal_selesai'],
                'alasan' => $data['alasan'],
                'file_pendukung' => $data['file_pendukung'] ?? $pengajuan->file_pendukung,
                'status' => 'pending',
                'catatan_revisi' => $revisiNote,
            ]);

            ApprovalCuti::where('pengajuan_id', $pengajuan->id)
                ->where('level_approval', $pengajuan->current_level)
                ->update([
                    'status_approval' => 'pending',
                    'catatan_reject' => null,
                    'tanggal_approval' => null,
                ]);

            $this->notifyCurrentApprover($pengajuan);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function notifyCurrentApprover(PengajuanCuti $pengajuan)
    {
        $approval = $pengajuan->getCurrentApprover();

        if ($approval && $approval->approver) {
            $approval->approver->notify(new CutiNeedApprovalNotification($pengajuan));
            $approval->markAsNotified();
        }
    }

    private function deductCutiQuota(PengajuanCuti $pengajuan)
    {
        $durasi = $pengajuan->durasi;
        $user = $pengajuan->user;

        if ($user->sisa_cuti >= $durasi) {
            $user->decrement('sisa_cuti', $durasi);
        }
    }
}
