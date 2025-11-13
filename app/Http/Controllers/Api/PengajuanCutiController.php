<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengajuanCuti;
use App\Models\JenisCuti;
use App\Services\ApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PengajuanCutiController extends Controller
{
    protected $approvalService;

    public function __construct(ApprovalService $approvalService)
    {
        $this->approvalService = $approvalService;
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $query = PengajuanCuti::with(['user', 'jenisCuti', 'divisi', 'jabatan', 'approvals.approver']);

        if (!$user->isAdmin() && !$user->isHRD() && !$user->isPersonalia()) {
            $query->where('user_id', $user->id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('start_date')) {
            $query->whereDate('tanggal_mulai', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->whereDate('tanggal_selesai', '<=', $request->end_date);
        }

        $pengajuan = $query->latest()->paginate(10);

        return response()->json($pengajuan);
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_cuti_id' => 'required|exists:jenis_cuti,id',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string|max:500',
            'file_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $user = $request->user();
        $jenisCuti = JenisCuti::findOrFail($request->jenis_cuti_id);

        // Validasi min hari pengajuan
        $daysDiff = Carbon::parse($request->tanggal_mulai)->diffInDays(now());
        if ($daysDiff < $jenisCuti->min_hari_pengajuan) {
            return response()->json([
                'message' => "Pengajuan minimal {$jenisCuti->min_hari_pengajuan} hari sebelum tanggal cuti."
            ], 422);
        }

        // Validasi dokumen
        if ($jenisCuti->perlu_dokumen && !$request->hasFile('file_pendukung')) {
            return response()->json([
                'message' => 'Dokumen pendukung wajib diupload untuk jenis cuti ini.'
            ], 422);
        }

        // Validasi sisa cuti
        $durasi = Carbon::parse($request->tanggal_mulai)->diffInDays(Carbon::parse($request->tanggal_selesai)) + 1;
        if ($user->sisa_cuti < $durasi) {
            return response()->json([
                'message' => "Sisa cuti tidak mencukupi. Sisa: {$user->sisa_cuti} hari, dibutuhkan: {$durasi} hari."
            ], 422);
        }

        $filePath = null;
        if ($request->hasFile('file_pendukung')) {
            $filePath = $request->file('file_pendukung')->store('cuti_documents', 'public');
        }

        $pengajuan = PengajuanCuti::create([
            'user_id' => $user->id,
            'jenis_cuti_id' => $request->jenis_cuti_id,
            'divisi_id' => $user->divisi_id,
            'jabatan_id' => $user->jabatan_id,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'alasan' => $request->alasan,
            'file_pendukung' => $filePath,
        ]);

        $this->approvalService->createApprovalFlow($pengajuan);

        return response()->json([
            'message' => 'Pengajuan cuti berhasil dibuat.',
            'data' => $pengajuan->load(['jenisCuti', 'approvals.approver']),
        ], 201);
    }

    public function show($id)
    {
        $pengajuan = PengajuanCuti::with([
            'user',
            'jenisCuti',
            'divisi',
            'jabatan',
            'approvals.approver.jabatan'
        ])->findOrFail($id);

        return response()->json($pengajuan);
    }

    public function update(Request $request, $id)
    {
        $pengajuan = PengajuanCuti::findOrFail($id);

        if ($pengajuan->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($pengajuan->status !== 'ditolak') {
            return response()->json([
                'message' => 'Pengajuan hanya bisa direvisi jika ditolak.'
            ], 422);
        }

        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string|max:500',
            'file_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only(['tanggal_mulai', 'tanggal_selesai', 'alasan']);

        if ($request->hasFile('file_pendukung')) {
            if ($pengajuan->file_pendukung) {
                Storage::disk('public')->delete($pengajuan->file_pendukung);
            }
            $data['file_pendukung'] = $request->file('file_pendukung')->store('cuti_documents', 'public');
        }

        $this->approvalService->revise($pengajuan, $data);

        return response()->json([
            'message' => 'Pengajuan berhasil direvisi.',
            'data' => $pengajuan->fresh()->load(['jenisCuti', 'approvals.approver']),
        ]);
    }

    public function destroy($id)
    {
        $pengajuan = PengajuanCuti::findOrFail($id);

        if ($pengajuan->user_id !== request()->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($pengajuan->status !== 'pending') {
            return response()->json([
                'message' => 'Hanya pengajuan pending yang bisa dibatalkan.'
            ], 422);
        }

        if ($pengajuan->file_pendukung) {
            Storage::disk('public')->delete($pengajuan->file_pendukung);
        }

        $pengajuan->delete();

        return response()->json([
            'message' => 'Pengajuan cuti berhasil dibatalkan.'
        ]);
    }
}
