<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HariLibur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HariLiburController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', now()->year);

        $hariLibur = HariLibur::where('tahun', $tahun)
            ->orderBy('tanggal')
            ->get();

        return response()->json($hariLibur);
    }

    public function syncFromApi(Request $request)
    {
        $tahun = $request->get('tahun', now()->year);

        try {
            $response = Http::get("https://api-harilibur.vercel.app/api", [
                'year' => $tahun,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                HariLibur::where('tahun', $tahun)->delete();

                $count = 0;
                foreach ($data as $item) {
                    if ($item['is_national_holiday']) {
                        HariLibur::create([
                            'tanggal' => $item['holiday_date'],
                            'nama_libur' => $item['holiday_name'],
                            'tahun' => $tahun,
                        ]);
                        $count++;
                    }
                }

                return response()->json([
                    'message' => "Berhasil sync {$count} hari libur untuk tahun {$tahun}",
                ]);
            }

            return response()->json([
                'message' => 'Gagal mengambil data dari API'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
