<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Usulan Koreksi DTKS/DTSEN - Desa Palalang</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000000;
            background: #f1f5f9;
            padding: 2rem 0;
            font-size: 11pt;
            line-height: 1.4;
        }

        .paper-sheet {
            background: #ffffff;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 20mm 25mm;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        /* Kop Surat Resmi Desa */
        .kop-surat {
            text-align: center;
            border-bottom: 3px double #000000;
            padding-bottom: 10px;
            margin-bottom: 20px;
            position: relative;
        }

        .kop-title-1 {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
        }

        .kop-title-2 {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .kop-title-3 {
            font-size: 16pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin: 2px 0;
        }

        .kop-alamat {
            font-size: 9.5pt;
            font-style: italic;
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        /* Official Letter Metadata */
        .meta-surat {
            margin-bottom: 18px;
            font-size: 11pt;
        }

        .table-laporan {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 10pt;
        }

        .table-laporan th, .table-laporan td {
            border: 1px solid #000000;
            padding: 6px 8px;
        }

        .table-laporan th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }

        /* Tanda Tangan */
        .ttd-container {
            margin-top: 35px;
            page-break-inside: avoid;
        }

        /* Floating action buttons */
        .action-bar {
            position: fixed;
            bottom: 25px;
            right: 25px;
            z-index: 1050;
            display: flex;
            gap: 10px;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .paper-sheet {
                box-shadow: none;
                padding: 10mm 15mm;
                width: 100%;
            }
            .action-bar {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Floating Print Controls -->
    <div class="action-bar d-print-none">
        <button onclick="window.print()" class="btn btn-primary btn-lg rounded-pill shadow-lg px-4 py-2 fw-bold">
            <i class="bi bi-printer-fill me-2"></i> Cetak / Simpan PDF
        </button>
        <button onclick="window.close()" class="btn btn-dark btn-lg rounded-pill shadow-lg px-3 py-2">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <div class="paper-sheet">
        <!-- KOP SURAT RESMI -->
        <div class="kop-surat">
            <div class="kop-title-1">PEMERINTAH KABUPATEN {{ strtoupper($setting->kabupaten ?? 'PAMEKASAN') }}</div>
            <div class="kop-title-2">KECAMATAN {{ strtoupper($setting->kecamatan ?? 'PAKONG') }}</div>
            <div class="kop-title-3">PEMERINTAH DESA PALALANG</div>
            <div class="kop-alamat">
                {{ $setting->alamat_kantor ?? 'Jl. Balai Desa Palalang No. 01' }} &bull; Kode Pos: {{ $setting->kode_pos ?? '69352' }}<br>
                Email: {{ $setting->email_desa ?? 'pemdes.palalang@pamekasankab.go.id' }} &bull; Kontak: {{ $setting->telepon_desa ?? '0812-3456-7890' }}
            </div>
        </div>

        <!-- NOMOR SURAT & PERIHAL -->
        <table class="w-100 meta-surat">
            <tr>
                <td style="width: 110px;">Nomor</td>
                <td style="width: 10px;">:</td>
                <td>400.9 / {{ date('Y') }}.{{ rand(100, 999) }} / 432.508.{{ rand(10, 99) }} / {{ date('Y') }}</td>
                <td class="text-end" style="width: 200px;">Palalang, {{ $tanggalCetak }}</td>
            </tr>
            <tr>
                <td>Sifat</td>
                <td>:</td>
                <td>Penting / Resmi</td>
                <td class="text-end">Kepada Yth.</td>
            </tr>
            <tr>
                <td>Lampiran</td>
                <td>:</td>
                <td>1 (Satu) Berkas Lembar Rekomendasi</td>
                <td class="text-end"><strong>Bapak Camat {{ $setting->kecamatan ?? 'Pakong' }} / Dinsos Kab. Pamekasan</strong></td>
            </tr>
            <tr>
                <td class="align-top">Perihal</td>
                <td class="align-top">:</td>
                <td class="align-top">
                    <strong>Usulan Penetapan & Prioritas Koreksi Data Rumah Tangga Miskin/Rentan pada DTKS/DTSEN (Program PKH & BPNT) Hasil Analisis SPK BWM-MARCOS</strong>
                </td>
                <td class="text-end align-top">di - <br><u>T E M P A T</u></td>
            </tr>
        </table>

        <!-- ISI SURAT DINAS -->
        <p style="text-align: justify; text-indent: 35px; margin-top: 15px;">
            Berdasarkan hasil verifikasi faktual lapangan kondisi sosial-ekonomi riil masyarakat serta pemrosesan komputasi <em>Sistem Pendukung Keputusan (SPK)</em> menggunakan metode <strong>Best Worst Method (BWM)</strong> dan <strong>Measurement of Alternatives and Ranking according to COmpromise Solution (MARCOS)</strong> untuk mengatasi anomali data (<em>Exclusion Error</em>) pada Data Terpadu Kesejahteraan Sosial (DTKS/DTSEN), dengan ini Pemerintah Desa Palalang menyampaikan daftar usulan prioritas rumah tangga yang mendesak didampingi dalam perubahan peringkat dan pengajuan perbaikan data bansos (PKH/BPNT) sebagai berikut:
        </p>

        <!-- TABEL HASIL PENETAPAN REKOMENDASI -->
        <table class="table-laporan">
            <thead>
                <tr>
                    <th style="width: 40px;">Prioritas</th>
                    <th>Nama Kepala Keluarga</th>
                    <th style="width: 130px;">NIK</th>
                    <th>Dusun / Wilayah</th>
                    <th style="width: 70px;">RT/RW</th>
                    <th style="width: 85px;">Skor f(K)</th>
                    <th style="width: 140px;">Rekomendasi Tindak Lanjut</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hasil as $h)
                    @php
                        $isLolos = $h->peringkat <= $kuota;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $h->peringkat }}</td>
                        <td><strong>{{ $h->warga->nama_kk ?? '-' }}</strong></td>
                        <td class="text-center" style="font-family: monospace;">{{ $h->warga->nik ?? '-' }}</td>
                        <td>{{ $h->warga->dusun ?? '-' }}</td>
                        <td class="text-center">{{ $h->warga->rt_rw ?? '-' }}</td>
                        <td class="text-center fw-bold">{{ number_format($h->skor_utilitas, 5) }}</td>
                        <td class="text-center">
                            @if($isLolos)
                                <strong>Prioritas Utama (Usul Sanggah)</strong>
                            @else
                                Usulan Lanjutan / Cadangan
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-3">Tidak ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <p style="text-align: justify; text-indent: 35px; margin-top: 15px;">
            Daftar usulan ini ditetapkan secara objektif, terukur, dan transparan melalui musyawarah desa serta perhitungan ilmiah multi-kriteria untuk menjadi dasar pendampingan operator desa dalam pemutakhiran data pada aplikasi SIKS-NG dan usulan resmi ke Dinas Sosial Kabupaten Pamekasan.
        </p>

        <!-- KOLOM TANDA TANGAN RESMI -->
        <div class="row ttd-container">
            <div class="col-6 text-center">
                Mengetahui,<br>
                <strong>Ketua Badan Permusyawaratan Desa (BPD)</strong><br>
                Desa Palalang<br><br><br><br>
                <u><strong>{{ $setting->nama_bpd ?? 'K.H. Zainal Arifin' }}</strong></u><br>
                Tokoh Masyarakat
            </div>
            <div class="col-6 text-center">
                Palalang, {{ $tanggalCetak }}<br>
                <strong>Kepala Desa Palalang</strong><br>
                Kecamatan {{ $setting->kecamatan ?? 'Pakong' }}<br><br><br><br>
                <u><strong>{{ $setting->nama_kades ?? 'H. Moh. Fadli, S.Pd.' }}</strong></u><br>
                NIP. {{ $setting->nip_kades ?? '19780514 200501 1 004' }}
            </div>
        </div>
    </div>

</body>
</html>
