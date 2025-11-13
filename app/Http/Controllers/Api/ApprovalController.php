<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengajuanCuti;
use App\Models\ApprovalCuti;
use App\Services\ApprovalService;
use App\Helpers\ApprovalHelper;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    protected $approvalService;

    public function __construct(ApprovalService $approvalService)
    {
        $this->approvalService = $approvalService;
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $approvals = ApprovalCuti::with(['pengajuan.user', 'pengajuan.jenisCuti', 'pengajuan.divisi'])
            ->where('approver_id', $user->id)
            ->where('status_approval', 'pending')
            ->whereHas('pengajuan', function($q) {
                $q->where('status', 'pending')
                  ->whereRaw('current_level = approval_cuti.level_approval');
            })
            ->latest()
            ->paginate(10);

        return response()->json($approvals);
    }

    public function approve(Request $request, $pengajuanId)
    {
        $request->validate([
            'catatan' => 'nullable|string|max:500',
        ]);

        $user = $request->user();
        $pengajuan = PengajuanCuti::findOrFail($pengajuanId);

        if (!ApprovalHelper::canApprove($user->id, $pengajuan)) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk approve pengajuan ini.'
            ], 403);
        }

        try {
            $this->approvalService->approve($pengajuan, $user->id, $request->catatan);

            return response()->json([
                'message' => 'Pengajuan berhasil disetujui.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request, $pengajuanId)
    {
        $request->validate([
            'catatan_reject' => 'required|string|max:500',
        ]);

        $user = $request->user();
        $pengajuan = PengajuanCuti::findOrFail($pengajuanId);

        if (!ApprovalHelper::canApprove($user->id, $pengajuan)) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk reject pengajuan ini.'
            ], 403);
        }

        try {
            $this->approvalService->reject($pengajuan, $user->id, $request->catatan_reject);

            return response()->json([
                'message' => 'Pengajuan ditolak.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function history($pengajuanId)
    {
        $approvals = ApprovalCuti::with(['approver.jabatan'])
            ->where('pengajuan_id', $pengajuanId)
            ->orderBy('level_approval')
            ->get();

        return response()->json($approvals);
    }
}
