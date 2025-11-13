<?php

namespace App\Http\Controllers\Web;

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
        $pengajuan = PengajuanCuti::with(['jenisCuti', 'approvals.approver'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return view('pengajuan.index', compact('pengajuan'));
    }

    public function create()
    {
        $jenisCuti = JenisCuti::all();
        return view('pengajuan.create', compact('jenisCuti'));
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

        $daysDiff = Carbon::parse($request->tanggal_mulai)->diffInDays(now());
        if ($daysDiff < $jenisCuti->min_hari_pengajuan) {
            return back()->with('error', "Pengajuan minimal {$jenisCuti->min_hari_pengajuan} hari sebelum tanggal cuti.");
        }

        if ($jenisCuti->perlu_dokumen && !$request->hasFile('file_pendukung')) {
            return back()->with('error', 'Dokumen pendukung wajib diupload untuk jenis cuti ini.');
        }

        $durasi = Carbon::parse($request->tanggal_mulai)->diffInDays(Carbon::parse($request->tanggal_selesai)) + 1;
        if ($user->sisa_cuti < $durasi) {
            return back()->with('error', "Sisa cuti tidak mencukupi. Sisa: {$user->sisa_cuti} hari, dibutuhkan: {$durasi} hari.");
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

        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan cuti berhasil dibuat!');
    }

    public function show($id)
    {
        $pengajuan = PengajuanCuti::with(['jenisCuti', 'approvals.approver.jabatan'])
            ->where('user_id', request()->user()->id)
            ->findOrFail($id);

        return view('pengajuan.show', compact('pengajuan'));
    }

    public function edit($id)
    {
        $pengajuan = PengajuanCuti::where('user_id', request()->user()->id)
            ->where('status', 'ditolak')
            ->findOrFail($id);

        $jenisCuti = JenisCuti::all();

        return view('pengajuan.edit', compact('pengajuan', 'jenisCuti'));
    }

    public function update(Request $request, $id)
    {
        $pengajuan = PengajuanCuti::where('user_id', $request->user()->id)
            ->where('status', 'ditolak')
            ->findOrFail($id);

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

        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil direvisi!');
    }

    public function destroy($id)
    {
        $pengajuan = PengajuanCuti::where('user_id', request()->user()->id)
            ->where('status', 'pending')
            ->findOrFail($id);

        if ($pengajuan->file_pendukung) {
            Storage::disk('public')->delete($pengajuan->file_pendukung);
        }

        $pengajuan->delete();

        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil dibatalkan!');
    }
}
