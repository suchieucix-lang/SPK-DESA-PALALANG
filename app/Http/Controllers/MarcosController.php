<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\HasilMarcos;
use App\Models\PengaturanDesa;
use App\Services\MarcosService;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class MarcosController extends Controller
{
    protected MarcosService $marcosService;

    public function __construct(MarcosService $marcosService)
    {
        $this->marcosService = $marcosService;
    }

    public function index(Request $request)
    {
        $kriteria = Kriteria::orderBy('kode', 'asc')->get();
        $warga = Warga::orderBy('nama_kk', 'asc')->get();
        $setting = PengaturanDesa::getSetting();

        if ($kriteria->isEmpty() || $warga->isEmpty()) {
            return view('marcos.empty', compact('kriteria', 'warga'));
        }

        // Siapkan struktur array untuk kalkulasi
        $altList = $warga->map(fn($w) => [
            'id' => $w->id_warga,
            'nik' => $w->nik,
            'nama_kk' => $w->nama_kk,
            'nama' => $w->nama_kk,
            'dusun' => $w->dusun,
            'rt_rw' => $w->rt_rw,
        ])->toArray();

        $critList = $kriteria->map(fn($c) => [
            'id' => $c->id_kriteria,
            'kode' => $c->kode,
            'nama' => $c->nama_kriteria,
            'jenis' => $c->jenis,
            'bobot' => (float)$c->bobot,
        ])->toArray();

        $matrix = [];
        $penilaianAll = Penilaian::all();
        foreach ($penilaianAll as $p) {
            $matrix[$p->id_warga][$p->id_kriteria] = (float)$p->nilai_riil;
        }

        // Jalankan kalkulasi MARCOS
        $analysis = $this->marcosService->calculate($altList, $critList, $matrix);

        // Ambil ranking tersimpan dari database untuk verifikasi
        $savedResults = HasilMarcos::with('warga')->orderBy('peringkat', 'asc')->get();

        return view('marcos.index', compact(
            'kriteria',
            'warga',
            'setting',
            'analysis',
            'savedResults'
        ));
    }

    public function calculateAndSave(Request $request)
    {
        $engine = $request->input('engine', 'php');
        $kriteria = Kriteria::orderBy('kode', 'asc')->get();
        $warga = Warga::orderBy('nama_kk', 'asc')->get();

        $altList = $warga->map(fn($w) => [
            'id' => $w->id_warga,
            'nik' => $w->nik,
            'nama_kk' => $w->nama_kk,
            'nama' => $w->nama_kk,
            'dusun' => $w->dusun,
            'rt_rw' => $w->rt_rw,
        ])->toArray();

        $critList = $kriteria->map(fn($c) => [
            'id' => $c->id_kriteria,
            'kode' => $c->kode,
            'nama' => $c->nama_kriteria,
            'jenis' => $c->jenis,
            'bobot' => (float)$c->bobot,
        ])->toArray();

        $matrix = [];
        foreach (Penilaian::all() as $p) {
            $matrix[$p->id_warga][$p->id_kriteria] = (float)$p->nilai_riil;
        }

        $engineUsed = 'PHP Native Engine';

        if ($engine === 'python') {
            try {
                $pyScript = app_path('Services/Python/bwm_marcos_engine.py');
                $inputJson = json_encode([
                    'action' => 'marcos',
                    'payload' => [
                        'alternatives' => $altList,
                        'criteria' => $critList,
                        'matrix' => $matrix,
                    ]
                ]);

                $process = new Process(['python', $pyScript]);
                $process->setInput($inputJson);
                $process->run();

                if ($process->isSuccessful()) {
                    $pyOutput = json_decode($process->getOutput(), true);
                    if ($pyOutput && isset($pyOutput['success']) && $pyOutput['success']) {
                        HasilMarcos::truncate();
                        foreach ($pyOutput['rankings'] as $r) {
                            HasilMarcos::create([
                                'id_warga' => $r['id_warga'],
                                'skor_utilitas' => $r['skor_utilitas'],
                                'peringkat' => $r['peringkat'],
                                'k_plus' => $r['k_plus'] ?? null,
                                'k_minus' => $r['k_minus'] ?? null,
                            ]);
                        }
                        return redirect()->route('marcos.index')
                            ->with('success', 'Kalkulasi MARCOS berhasil dieksekusi dengan Python Hybrid Engine dan disimpan ke basis data!');
                    }
                }
            } catch (\Exception $e) {
                // fallback to PHP
            }
        }

        // Native PHP Calculation & Database Commit
        $analysis = $this->marcosService->calculate($altList, $critList, $matrix);

        if ($analysis['success']) {
            HasilMarcos::truncate();
            foreach ($analysis['rankings'] as $item) {
                HasilMarcos::create([
                    'id_warga' => $item['id_warga'],
                    'skor_utilitas' => $item['skor_utilitas'],
                    'peringkat' => $item['peringkat'],
                    'k_plus' => $item['k_plus'],
                    'k_minus' => $item['k_minus'],
                ]);
            }

            return redirect()->route('marcos.index')
                ->with('success', 'Kalkulasi MARCOS berhasil diperbarui dan disimpan ke basis data.');
        }

        return redirect()->route('marcos.index')
            ->with('error', 'Gagal memproses kalkulasi MARCOS: ' . ($analysis['message'] ?? 'Data tidak valid.'));
    }
}

