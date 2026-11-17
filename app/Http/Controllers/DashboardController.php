<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\Warga;
use App\Models\BwmPreferensi;
use App\Models\HasilMarcos;
use App\Models\PengaturanDesa;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalWarga = Warga::count();
        $totalKriteria = Kriteria::count();
        $setting = PengaturanDesa::getSetting();
        $kuota = $setting->kuota_bantuan ?? 10;

        // Cek status validitas BWM
        $bestCrit = BwmPreferensi::where('is_best', true)->with('kriteria')->first();
        $worstCrit = BwmPreferensi::where('is_worst', true)->with('kriteria')->first();
        $totalWeights = Kriteria::sum('bobot');
        $hasBwm = $bestCrit && $worstCrit && abs($totalWeights - 1.0) < 0.05;

        // Ambil hasil MARCOS
        $hasilMarcos = HasilMarcos::with('warga')->orderBy('peringkat', 'asc')->get();
        $totalHasil = $hasilMarcos->count();

        // Kategori Kerentanan berdasarkan skor utilitas f(K)
        // Kategori 1 (Sangat Rentan / Prioritas 1): Skor >= 0.55 atau masuk dalam kuota
        // Kategori 2 (Rentan / Prioritas 2): Skor 0.40 - 0.54
        // Kategori 3 (Cukup Rentan / Cadangan): Skor < 0.40
        $kategori1 = 0; // Sangat Rentan
        $kategori2 = 0; // Rentan
        $kategori3 = 0; // Cukup Rentan

        foreach ($hasilMarcos as $h) {
            $score = $h->skor_utilitas;
            if ($score >= 0.55 || $h->peringkat <= ($kuota / 2)) {
                $kategori1++;
            } elseif ($score >= 0.40 || $h->peringkat <= $kuota) {
                $kategori2++;
            } else {
                $kategori3++;
            }
        }

        // Distribusi Warga per Dusun
        $dusunStats = Warga::selectRaw('dusun, count(*) as total')
            ->groupBy('dusun')
            ->pluck('total', 'dusun')
            ->toArray();

        // 5 Teratas Penerima Rekomendasi
        $topWarga = $hasilMarcos->take(5);

        return view('dashboard.index', compact(
            'totalWarga',
            'totalKriteria',
            'setting',
            'kuota',
            'bestCrit',
            'worstCrit',
            'hasBwm',
            'totalHasil',
            'kategori1',
            'kategori2',
            'kategori3',
            'dusunStats',
            'topWarga',
            'totalWeights'
        ));
    }

    public function panduan()
    {
        $kriteria = Kriteria::orderBy('kode', 'asc')->get();
        $setting = PengaturanDesa::getSetting();
        $sampleWarga = Warga::with('penilaian')->take(3)->get();
        return view('panduan.index', compact('kriteria', 'setting', 'sampleWarga'));
    }
}

