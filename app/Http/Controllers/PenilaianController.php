<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use App\Models\Kriteria;
use App\Models\Penilaian;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    public function index(Request $request)
    {
        $kriteria = Kriteria::orderBy('kode', 'asc')->get();
        $dusunList = Warga::select('dusun')->distinct()->pluck('dusun');

        $query = Warga::with('penilaian');

        if ($request->filled('dusun')) {
            $query->where('dusun', $request->dusun);
        }

        $warga = $query->orderBy('nama_kk', 'asc')->get();

        // Build matrix [id_warga][id_kriteria] => nilai_riil
        $matrix = [];
        $penilaianAll = Penilaian::all();
        foreach ($penilaianAll as $p) {
            $matrix[$p->id_warga][$p->id_kriteria] = $p->nilai_riil;
        }

        return view('penilaian.index', compact('kriteria', 'warga', 'matrix', 'dusunList'));
    }

    public function batchUpdate(Request $request)
    {
        $request->validate([
            'nilai' => 'required|array',
        ]);

        $count = 0;
        foreach ($request->nilai as $id_warga => $kriteriaVals) {
            if (is_array($kriteriaVals)) {
                foreach ($kriteriaVals as $id_kriteria => $val) {
                    if ($val !== null && $val !== '') {
                        Penilaian::updateOrCreate(
                            [
                                'id_warga' => $id_warga,
                                'id_kriteria' => $id_kriteria,
                            ],
                            [
                                'nilai_riil' => (float)$val,
                            ]
                        );
                        $count++;
                    }
                }
            }
        }

        return redirect()->route('penilaian.index')
            ->with('success', "Matriks penilaian warga berhasil diperbarui ({$count} data tersimpan).");
    }

    public function updateSingle(Request $request)
    {
        $request->validate([
            'id_warga' => 'required|exists:warga,id_warga',
            'nilai' => 'required|array',
        ]);

        $id_warga = $request->id_warga;
        foreach ($request->nilai as $id_kriteria => $val) {
            if ($val !== null && $val !== '') {
                Penilaian::updateOrCreate(
                    [
                        'id_warga' => $id_warga,
                        'id_kriteria' => $id_kriteria,
                    ],
                    [
                        'nilai_riil' => (float)$val,
                    ]
                );
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Penilaian warga berhasil disimpan.',
        ]);
    }
}

