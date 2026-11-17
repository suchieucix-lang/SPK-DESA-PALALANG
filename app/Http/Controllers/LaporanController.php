<?php

namespace App\Http\Controllers;

use App\Models\HasilMarcos;
use App\Models\Warga;
use App\Models\Kriteria;
use App\Models\PengaturanDesa;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $setting = PengaturanDesa::getSetting();
        $dusunList = Warga::select('dusun')->distinct()->pluck('dusun');
        $kuota = (int)($request->input('kuota', $setting->kuota_bantuan ?? 10));

        $query = HasilMarcos::with(['warga.penilaian.kriteria'])
            ->orderBy('peringkat', 'asc');

        if ($request->filled('dusun') && $request->dusun !== 'all') {
            $query->whereHas('warga', function ($q) use ($request) {
                $q->where('dusun', $request->dusun);
            });
        }

        if ($request->filled('filter_status')) {
            if ($request->filter_status === 'prioritas') {
                $query->where('peringkat', '<=', $kuota);
            } elseif ($request->filter_status === 'cadangan') {
                $query->where('peringkat', '>', $kuota);
            }
        }

        $hasil = $query->get();
        $kriteria = Kriteria::orderBy('kode', 'asc')->get();

        return view('laporan.index', compact('hasil', 'setting', 'dusunList', 'kriteria', 'kuota'));
    }

    public function cetak(Request $request)
    {
        $setting = PengaturanDesa::getSetting();
        $kuota = (int)($request->input('kuota', $setting->kuota_bantuan ?? 10));
        $dusun = $request->input('dusun');
        $filterStatus = $request->input('filter_status', 'all');

        $query = HasilMarcos::with(['warga.penilaian.kriteria'])
            ->orderBy('peringkat', 'asc');

        if ($dusun && $dusun !== 'all') {
            $query->whereHas('warga', function ($q) use ($dusun) {
                $q->where('dusun', $dusun);
            });
        }

        if ($filterStatus === 'prioritas') {
            $query->where('peringkat', '<=', $kuota);
        } elseif ($filterStatus === 'cadangan') {
            $query->where('peringkat', '>', $kuota);
        }

        $hasil = $query->get();
        $kriteria = Kriteria::orderBy('kode', 'asc')->get();
        $tanggalCetak = now()->locale('id')->translatedFormat('d F Y');

        return view('laporan.cetak', compact(
            'hasil',
            'setting',
            'kriteria',
            'kuota',
            'dusun',
            'filterStatus',
            'tanggalCetak'
        ));
    }

    public function exportExcel(Request $request)
    {
        $setting = PengaturanDesa::getSetting();
        $kuota = (int)($request->input('kuota', $setting->kuota_bantuan ?? 10));
        $hasil = HasilMarcos::with(['warga.penilaian.kriteria'])->orderBy('peringkat', 'asc')->get();
        $kriteria = Kriteria::orderBy('kode', 'asc')->get();

        $filename = "Laporan_SPK_BWM_MARCOS_Desa_Palalang_" . date('Ymd_His') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($hasil, $kriteria, $kuota, $setting) {
            $file = fopen('php://output', 'w');
            // Write BOM for UTF-8 in Excel
            fputs($file, "\xEF\xBB\xBF");

            // Header info
            fputcsv($file, ['PEMERINTAH KABUPATEN ' . strtoupper($setting->kabupaten ?? 'PAMEKASAN')]);
            fputcsv($file, ['KECAMATAN ' . strtoupper($setting->kecamatan ?? 'PAKONG') . ' - DESA ' . strtoupper($setting->nama_desa ?? 'PALALANG')]);
            fputcsv($file, ['DAFTAR HASIL PERANGKINGAN SPK KERENTANAN SOSIAL METODE BWM-MARCOS']);
            fputcsv($file, ['Tanggal Ekspor: ' . date('d-m-Y H:i:s')]);
            fputcsv($file, []);

            // Table headers
            $rowHeader = ['Peringkat', 'NIK', 'Nama Kepala Keluarga', 'Dusun', 'RT / RW'];
            foreach ($kriteria as $c) {
                $rowHeader[] = $c->kode . ' (' . $c->nama_kriteria . ')';
            }
            $rowHeader[] = 'Skor Utilitas f(K)';
            $rowHeader[] = 'Status Rekomendasi';

            fputcsv($file, $rowHeader);

            // Table rows
            foreach ($hasil as $h) {
                $status = ($h->peringkat <= $kuota) ? 'Prioritas Penerima (Kuota ' . $kuota . ')' : 'Cadangan';
                $row = [
                    $h->peringkat,
                    "'" . ($h->warga->nik ?? ''),
                    $h->warga->nama_kk ?? '',
                    $h->warga->dusun ?? '',
                    $h->warga->rt_rw ?? '',
                ];

                foreach ($kriteria as $c) {
                    $val = $h->warga ? $h->warga->getNilaiKriteria($c->id_kriteria) : '-';
                    $row[] = $val;
                }

                $row[] = number_format($h->skor_utilitas, 5);
                $row[] = $status;

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

