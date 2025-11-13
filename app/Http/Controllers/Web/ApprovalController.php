<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ApprovalCuti;
use App\Models\PengajuanCuti;
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

        $approvals = ApprovalCuti::with([
            'pengajuan.user',
            'pengajuan.jenisCuti',
            'pengajuan.divisi',
            'pengajuan.jabatan'
        ])
        ->where('approver_id', $user->id)
        ->where('status_approval', 'pending')
        ->whereHas('pengajuan', function($q) {
            $q->where('status', 'pending')
              ->whereRaw('current_level = approval_cuti.level_approval');
        })
        ->latest()
        ->paginate(10);

        return view('approval.index', compact('approvals'));
    }

    public function approve(Request $request, $id)
    {
        $user = $request->user();
        $pengajuan = PengajuanCuti::findOrFail($id);

        if (!ApprovalHelper::canApprove($user->id, $pengajuan)) {
            return back()->with('error', 'Anda tidak memiliki akses untuk approve pengajuan ini.');
        }

        try {
            $this->approvalService->approve($pengajuan, $user->id, $request->catatan);
            return redirect()->route('approval.index')->with('success', 'Pengajuan berhasil disetujui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'catatan_reject' => 'required|string|max:500',
        ]);

        $user = $request->user();
        $pengajuan = PengajuanCuti::findOrFail($id);

        if (!ApprovalHelper::canApprove($user->id, $pengajuan)) {
            return back()->with('error', 'Anda tidak memiliki akses untuk reject pengajuan ini.');
        }

        try {
            $this->approvalService->reject($pengajuan, $user->id, $request->catatan_reject);
            return redirect()->route('approval.index')->with('success', 'Pengajuan ditolak.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
