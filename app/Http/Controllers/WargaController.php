<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use App\Models\Kriteria;
use App\Models\Penilaian;
use Illuminate\Http\Request;

class WargaController extends Controller
{
    public function index(Request $request)
    {
        $dusunList = Warga::select('dusun')->distinct()->pluck('dusun');
        $kriteria = Kriteria::orderBy('kode', 'asc')->get();

        $query = Warga::with(['penilaian.kriteria', 'hasilMarcos']);

        if ($request->filled('dusun')) {
            $query->where('dusun', $request->dusun);
        }

        $warga = $query->orderBy('nama_kk', 'asc')->get();

        return view('warga.index', compact('warga', 'dusunList', 'kriteria'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|size:16|unique:warga,nik',
            'nama_kk' => 'required|string|max:255',
            'dusun' => 'required|string|max:255',
            'rt_rw' => 'required|string|max:50',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string|max:20',
            'nilai' => 'nullable|array',
        ]);

        $warga = Warga::create([
            'nik' => $request->nik,
            'nama_kk' => $request->nama_kk,
            'dusun' => $request->dusun,
            'rt_rw' => $request->rt_rw,
            'alamat' => $request->alamat,
            'no_telp' => $request->no_telp,
        ]);

        // Simpan nilai kriteria jika ada
        if ($request->has('nilai') && is_array($request->nilai)) {
            foreach ($request->nilai as $id_kriteria => $val) {
                if ($val !== null && $val !== '') {
                    Penilaian::updateOrCreate(
                        [
                            'id_warga' => $warga->id_warga,
                            'id_kriteria' => $id_kriteria,
                        ],
                        [
                            'nilai_riil' => (float)$val,
                        ]
                    );
                }
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data warga/KK berhasil ditambahkan.',
                'warga' => $warga,
            ]);
        }

        return redirect()->route('warga.index')->with('success', "Data KK {$warga->nama_kk} berhasil ditambahkan!");
    }

    public function update(Request $request, $id)
    {
        $warga = Warga::findOrFail($id);

        $request->validate([
            'nik' => 'required|string|size:16|unique:warga,nik,' . $warga->id_warga . ',id_warga',
            'nama_kk' => 'required|string|max:255',
            'dusun' => 'required|string|max:255',
            'rt_rw' => 'required|string|max:50',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string|max:20',
            'nilai' => 'nullable|array',
        ]);

        $warga->update([
            'nik' => $request->nik,
            'nama_kk' => $request->nama_kk,
            'dusun' => $request->dusun,
            'rt_rw' => $request->rt_rw,
            'alamat' => $request->alamat,
            'no_telp' => $request->no_telp,
        ]);

        if ($request->has('nilai') && is_array($request->nilai)) {
            foreach ($request->nilai as $id_kriteria => $val) {
                if ($val !== null && $val !== '') {
                    Penilaian::updateOrCreate(
                        [
                            'id_warga' => $warga->id_warga,
                            'id_kriteria' => $id_kriteria,
                        ],
                        [
                            'nilai_riil' => (float)$val,
                        ]
                    );
                }
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data warga berhasil diperbarui.',
                'warga' => $warga,
            ]);
        }

        return redirect()->route('warga.index')->with('success', "Data KK {$warga->nama_kk} berhasil diperbarui.");
    }

    public function destroy(Request $request, $id)
    {
        $warga = Warga::findOrFail($id);
        $nama = $warga->nama_kk;
        $warga->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Data KK {$nama} berhasil dihapus.",
            ]);
        }

        return redirect()->route('warga.index')->with('success', "Data KK {$nama} berhasil dihapus.");
    }

    /**
     * API detail data warga untuk modal edit
     */
    public function getDetail($id)
    {
        $warga = Warga::with('penilaian')->findOrFail($id);
        $penilaianMap = [];
        foreach ($warga->penilaian as $p) {
            $penilaianMap[$p->id_kriteria] = $p->nilai_riil;
        }

        return response()->json([
            'warga' => $warga,
            'penilaian' => $penilaianMap,
        ]);
    }
}

