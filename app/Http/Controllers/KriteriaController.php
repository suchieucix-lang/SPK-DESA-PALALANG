<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\BwmPreferensi;
use Illuminate\Http\Request;

class KriteriaController extends Controller
{
    public function index()
    {
        $kriteria = Kriteria::orderBy('kode', 'asc')->get();
        $totalBobot = $kriteria->sum('bobot');

        return view('kriteria.index', compact('kriteria', 'totalBobot'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:10|unique:kriteria,kode',
            'nama_kriteria' => 'required|string|max:255',
            'jenis' => 'required|in:benefit,cost',
            'keterangan' => 'nullable|string',
        ]);

        Kriteria::create([
            'kode' => strtoupper(trim($request->kode)),
            'nama_kriteria' => $request->nama_kriteria,
            'jenis' => $request->jenis,
            'bobot' => 0.0,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('kriteria.index')
            ->with('success', 'Kriteria baru berhasil ditambahkan! Silakan perbarui pembobotan BWM agar bobot kriteria terhitung.');
    }

    public function update(Request $request, $id)
    {
        $kriteria = Kriteria::findOrFail($id);

        $request->validate([
            'kode' => 'required|string|max:10|unique:kriteria,kode,' . $kriteria->id_kriteria . ',id_kriteria',
            'nama_kriteria' => 'required|string|max:255',
            'jenis' => 'required|in:benefit,cost',
            'keterangan' => 'nullable|string',
        ]);

        $kriteria->update([
            'kode' => strtoupper(trim($request->kode)),
            'nama_kriteria' => $request->nama_kriteria,
            'jenis' => $request->jenis,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('kriteria.index')
            ->with('success', "Kriteria {$kriteria->kode} berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $kriteria = Kriteria::findOrFail($id);
        $kode = $kriteria->kode;
        $kriteria->delete();

        return redirect()->route('kriteria.index')
            ->with('success', "Kriteria {$kode} berhasil dihapus.");
    }
}

