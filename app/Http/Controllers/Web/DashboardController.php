<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PengajuanCuti;
use App\Models\ApprovalCuti;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Stats
        $stats = [
            'sisa_cuti' => $user->sisa_cuti,
            'pending' => PengajuanCuti::where('user_id', $user->id)->where('status', 'pending')->count(),
            'disetujui' => PengajuanCuti::where('user_id', $user->id)->where('status', 'disetujui')->count(),
            'ditolak' => PengajuanCuti::where('user_id', $user->id)->where('status', 'ditolak')->count(),
        ];

        // Pending approvals
        $pendingApprovals = 0;
        if ($user->canApproveCuti()) {
            $pendingApprovals = ApprovalCuti::where('approver_id', $user->id)
                ->where('status_approval', 'pending')
                ->whereHas('pengajuan', function($q) {
                    $q->where('status', 'pending')
                      ->whereRaw('current_level = approval_cuti.level_approval');
                })
                ->count();
        }

        // Recent pengajuan
        $recentPengajuan = PengajuanCuti::with(['jenisCuti'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'pendingApprovals', 'recentPengajuan'));
    }
}
