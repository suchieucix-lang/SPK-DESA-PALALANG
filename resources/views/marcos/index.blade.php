@extends('layouts.app')

@section('title', 'Hasil Perangkingan')
@section('page_title', 'Hasil Prioritas Pendampingan Usulan Koreksi DTKS')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-12">

        <!-- Header Info Ringkas & Aksi Utama -->
        <div class="card-custom p-3.5 mb-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge px-3 py-1 rounded-pill" style="background-color: #ecfdf5; color: #047857; font-size: 0.75rem;">
                            <i class="bi bi-patch-check-fill me-1"></i> Rekomendasi Terhitung
                        </span>
                    </div>
                    <h5 class="fw-bold mb-1 text-dark">
                        Urutan Prioritas Pendampingan Usulan Koreksi DTKS / DTSEN
                    </h5>
                    <p class="text-muted mb-0" style="font-size: 0.85rem;">
                        Hasil perangkingan kompromi MARCOS berdasarkan fakta riil kondisi keluarga yang salah catat.
                    </p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <form action="{{ route('marcos.calculate') }}" method="POST">
                        @csrf
                        <input type="hidden" name="engine" value="php">
                        <button type="submit" class="btn btn-sm rounded-pill px-3 shadow-sm" style="background-color: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd;">
                            <i class="bi bi-arrow-repeat me-1"></i> Hitung Ulang
                        </button>
                    </form>
                    <a href="{{ route('laporan.export') }}" class="btn btn-sm rounded-pill px-3 shadow-sm text-white" style="background-color: #10b981; border: none;">
                        <i class="bi bi-file-earmark-excel me-1"></i> Unduh Excel
                    </a>
                    <a href="{{ route('laporan.cetak') }}" target="_blank" class="btn btn-sm rounded-pill px-3.5 shadow-sm fw-semibold text-white" style="background-color: #f43f5e; border: none;">
                        <i class="bi bi-printer-fill me-1"></i> Cetak Surat Resmi
                    </a>
                </div>
            </div>
        </div>

        <!-- Tab Navigasi Sederhana Soft -->
        <ul class="nav nav-pills nav-fill mb-3 p-1 rounded-pill border shadow-sm" style="background-color: #f8fafc;" id="marcosTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active rounded-pill fw-bold py-2" style="font-size: 0.88rem;" id="tab-publikasi-btn" data-bs-toggle="pill" data-bs-target="#tab-publikasi" type="button">
                    <i class="bi bi-award me-1"></i> 1. Daftar Penerima Rekomendasi
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link rounded-pill fw-semibold py-2 text-muted" style="font-size: 0.88rem;" id="tab-analisis-btn" data-bs-toggle="pill" data-bs-target="#tab-analisis" type="button">
                    <i class="bi bi-mortarboard me-1"></i> 2. Langkah Hitungan Matematis (Skripsi)
                </button>
            </li>
        </ul>

        <div class="tab-content" id="marcosTabsContent">
            <!-- TAB 1: HASIL REKOMENDASI (RAMAH PENGGUNA) -->
            <div class="tab-pane fade show active" id="tab-publikasi" role="tabpanel">
                <div class="card-custom">
                    <div class="card-header">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-check2-all me-2" style="color: #10b981;"></i>Daftar Peringkat Warga (Prioritas Tertinggi ke Terendah)</h6>
                            <small class="text-muted">Keluarga urutan 1 s.d {{ $setting->kuota_bantuan ?? 10 }} adalah calon prioritas utama pendampingan</small>
                        </div>
                        <span class="badge border px-3 py-1.5 rounded-pill" style="background-color: #f0fdf4; color: #047857; border-color: #bbf7d0 !important;">
                            Kuota Bantuan: {{ $setting->kuota_bantuan ?? 10 }} KK
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 datatable-init">
                                <thead class="table-light text-muted" style="font-size: 0.78rem; text-transform: uppercase;">
                                    <tr>
                                        <th style="width: 70px;" class="text-center">No</th>
                                        <th>Nama Kepala Keluarga</th>
                                        <th>NIK</th>
                                        <th>Wilayah Dusun</th>
                                        <th>RT / RW</th>
                                        <th class="text-center">Skor Kelayakan</th>
                                        <th class="text-center">Rekomendasi Tindak Lanjut</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 0.88rem;">
                                    @php
                                        $kuota = $setting->kuota_bantuan ?? 10;
                                    @endphp
                                    @forelse($analysis['rankings'] as $r)
                                        @php
                                            $isPrioritas = $r['peringkat'] <= $kuota;
                                        @endphp
                                        <tr class="{{ $isPrioritas ? 'table-light' : '' }}">
                                            <td class="text-center">
                                                @if($r['peringkat'] == 1)
                                                    <span class="badge rounded-circle p-2 px-2.5 fw-bold shadow-sm" style="background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a;">1</span>
                                                @elseif($r['peringkat'] == 2)
                                                    <span class="badge rounded-circle p-2 px-2.5 fw-bold shadow-sm" style="background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;">2</span>
                                                @elseif($r['peringkat'] == 3)
                                                    <span class="badge rounded-circle p-2 px-2.5 fw-bold shadow-sm" style="background-color: #ffedd5; color: #c2410c; border: 1px solid #fed7aa;">3</span>
                                                @else
                                                    <span class="badge border rounded-circle p-2 px-2.5 fw-bold" style="background-color: #f8fafc; color: #475569;">{{ $r['peringkat'] }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong class="text-dark">{{ $r['nama'] }}</strong>
                                            </td>
                                            <td>
                                                <code class="text-dark">{{ $r['nik'] }}</code>
                                            </td>
                                            <td>
                                                <span class="badge border" style="background-color: #f8fafc; color: #475569;">
                                                    {{ $r['dusun'] }}
                                                </span>
                                            </td>
                                            <td class="text-muted">{{ $r['rt_rw'] }}</td>
                                            <td class="text-center fw-bold" style="color: #0284c7;">
                                                {{ number_format($r['skor_utilitas'], 4) }}
                                            </td>
                                            <td class="text-center">
                                                @if($isPrioritas)
                                                    <span class="badge rounded-pill px-3 py-1.5 fw-semibold" style="background-color: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;">
                                                        <i class="bi bi-check-circle-fill me-1"></i> Prioritas Utama (Usul Sanggah)
                                                    </span>
                                                @else
                                                    <span class="badge rounded-pill px-3 py-1.5 fw-semibold" style="background-color: #f8fafc; color: #64748b; border: 1px solid #e2e8f0;">
                                                        <i class="bi bi-clock me-1"></i> Cadangan (Urutan {{ $r['peringkat'] }})
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">Belum ada data hasil perhitungan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: MODE ANALISIS AKADEMIS (UNTUK SKRIPSI) -->
            <div class="tab-pane fade" id="tab-analisis" role="tabpanel">
                <div class="alert border rounded-3 p-3 mb-3 d-flex align-items-center" style="background-color: #f8fafc;">
                    <i class="bi bi-info-circle-fill fs-4 me-3" style="color: #0284c7;"></i>
                    <div>
                        <strong class="text-dark d-block">Transparansi Perhitungan Matematis MARCOS</strong>
                        <small class="text-muted">
                            Bagian ini disediakan untuk kebutuhan verifikasi akademis (sidang skripsi), membuktikan bagaimana nilai dihitung dari data mentah sampai skor akhir kompromi.
                        </small>
                    </div>
                </div>

                <div class="accordion" id="accordionMath">
                    <!-- Step 1 -->
                    <div class="accordion-item rounded-3 border mb-2">
                        <h2 class="accordion-header">
                            <button class="accordion-button py-2.5 px-3 bg-light text-dark fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#step1Collapse">
                                1. Matriks Keputusan Mentah (X) & Bobot Kriteria (w)
                            </button>
                        </h2>
                        <div id="step1Collapse" class="accordion-collapse collapse show" data-bs-parent="#accordionMath">
                            <div class="accordion-body p-3">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm text-center mb-0" style="font-size: 0.8rem;">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-start">Nama Kepala Keluarga</th>
                                                @foreach($analysis['criteria'] as $c)
                                                    <th>{{ $c['kode'] }} ({{ ucfirst($c['jenis']) }})</th>
                                                @endforeach
                                            </tr>
                                            <tr style="background-color: #f0f9ff; color: #0284c7;">
                                                <th class="text-start">Bobot Kriteria (w)</th>
                                                @foreach($analysis['criteria'] as $c)
                                                    <th>{{ number_format($analysis['weights'][$c['id']] ?? 0, 4) }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($analysis['alternatives'] as $alt)
                                                <tr>
                                                    <td class="text-start fw-semibold">{{ $alt['nama'] }}</td>
                                                    @foreach($analysis['criteria'] as $c)
                                                        <td>{{ number_format($analysis['raw_matrix_X'][$alt['id']][$c['id']] ?? 0, 2) }}</td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="accordion-item rounded-3 border mb-2">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed py-2.5 px-3 bg-light text-dark fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#step2Collapse">
                                2. Penentuan Nilai Ideal (AI) & Anti-Ideal (AAI)
                            </button>
                        </h2>
                        <div id="step2Collapse" class="accordion-collapse collapse" data-bs-parent="#accordionMath">
                            <div class="accordion-body p-3">
                                <p class="text-muted mb-2" style="font-size: 0.8rem;">
                                    Untuk kriteria Cost: Nilai terkecil = Ideal (AI), nilai terbesar = Anti-Ideal (AAI). Sebaliknya untuk Benefit.
                                </p>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm text-center mb-0" style="font-size: 0.82rem;">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-start">Solusi Acuan</th>
                                                @foreach($analysis['criteria'] as $c)
                                                    <th>{{ $c['kode'] }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr style="background-color: #ecfdf5; color: #047857;" class="fw-bold">
                                                <td class="text-start">Ideal (AI)</td>
                                                @foreach($analysis['criteria'] as $c)
                                                    <td>{{ number_format($analysis['AI'][$c['id']] ?? 0, 2) }}</td>
                                                @endforeach
                                            </tr>
                                            <tr style="background-color: #fff1f2; color: #be123c;" class="fw-bold">
                                                <td class="text-start">Anti-Ideal (AAI)</td>
                                                @foreach($analysis['criteria'] as $c)
                                                    <td>{{ number_format($analysis['AAI'][$c['id']] ?? 0, 2) }}</td>
                                                @endforeach
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3 & 4 -->
                    <div class="accordion-item rounded-3 border mb-2">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed py-2.5 px-3 bg-light text-dark fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#step3Collapse">
                                3. Matriks Normalisasi (N) & Matriks Terbobot (V)
                            </button>
                        </h2>
                        <div id="step3Collapse" class="accordion-collapse collapse" data-bs-parent="#accordionMath">
                            <div class="accordion-body p-3">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm text-center mb-0" style="font-size: 0.8rem;">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-start">Alternatif</th>
                                                @foreach($analysis['criteria'] as $c)
                                                    <th>Vektor Terbobot {{ $c['kode'] }}</th>
                                                @endforeach
                                                <th style="background-color: #f0f9ff; color: #0284c7;">Jumlah (S_i)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr style="background-color: #fff1f2; color: #be123c;">
                                                <td class="text-start fw-bold">AAI</td>
                                                @foreach($analysis['criteria'] as $c)
                                                    <td>{{ number_format($analysis['weighted_matrix_V']['AAI'][$c['id']] ?? 0, 4) }}</td>
                                                @endforeach
                                                <td class="fw-bold">{{ number_format($analysis['S_aai'], 4) }}</td>
                                            </tr>
                                            @foreach($analysis['alternatives'] as $alt)
                                                <tr>
                                                    <td class="text-start">{{ $alt['nama'] }}</td>
                                                    @foreach($analysis['criteria'] as $c)
                                                        <td>{{ number_format($analysis['weighted_matrix_V'][$alt['id']][$c['id']] ?? 0, 4) }}</td>
                                                    @endforeach
                                                    <td class="fw-bold" style="color: #0284c7;">{{ number_format($analysis['S'][$alt['id']] ?? 0, 4) }}</td>
                                                </tr>
                                            @endforeach
                                            <tr style="background-color: #ecfdf5; color: #047857;">
                                                <td class="text-start fw-bold">AI</td>
                                                @foreach($analysis['criteria'] as $c)
                                                    <td>{{ number_format($analysis['weighted_matrix_V']['AI'][$c['id']] ?? 0, 4) }}</td>
                                                @endforeach
                                                <td class="fw-bold">{{ number_format($analysis['S_ai'], 4) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 5 & 6 -->
                    <div class="accordion-item rounded-3 border">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed py-2.5 px-3 bg-light text-dark fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#step5Collapse">
                                4. Derajat Utilitas (K+, K-) & Fungsi Utilitas Kompromi f(K)
                            </button>
                        </h2>
                        <div id="step5Collapse" class="accordion-collapse collapse" data-bs-parent="#accordionMath">
                            <div class="accordion-body p-3">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm text-center mb-0" style="font-size: 0.82rem;">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-start">Nama Kepala Keluarga</th>
                                                <th>K- (vs AAI)</th>
                                                <th>K+ (vs AI)</th>
                                                <th>f(K-)</th>
                                                <th>f(K+)</th>
                                                <th style="background-color: #f0f9ff; color: #0284c7;" class="fw-bold">Skor f(K)</th>
                                                <th style="background-color: #fef3c7; color: #b45309;" class="fw-bold">Ranking</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($analysis['rankings'] as $r)
                                                <tr>
                                                    <td class="text-start fw-bold">{{ $r['nama'] }}</td>
                                                    <td>{{ number_format($r['k_minus'], 4) }}</td>
                                                    <td>{{ number_format($r['k_plus'], 4) }}</td>
                                                    <td>{{ number_format($r['f_k_minus'], 4) }}</td>
                                                    <td>{{ number_format($r['f_k_plus'], 4) }}</td>
                                                    <td class="fw-bold" style="color: #0284c7;">{{ number_format($r['skor_utilitas'], 5) }}</td>
                                                    <td><span class="badge border" style="background-color: #f8fafc; color: #334155;">Rank {{ $r['peringkat'] }}</span></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
