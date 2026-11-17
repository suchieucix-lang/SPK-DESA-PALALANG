@extends('layouts.app')

@section('title', 'Beranda')
@section('page_title', 'Beranda Utama')

@section('content')
<!-- Kartu Selamat Datang Warna Soft Pastel -->
<div class="guide-banner-soft p-4 mb-4">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge px-3 py-1.5 rounded-pill" style="background-color: #dbeafe; color: #1e40af; font-size: 0.75rem;">
                    <i class="bi bi-shield-check me-1"></i> Penanganan Exclusion Error DTKS
                </span>
            </div>
            <h4 class="fw-bold text-dark mb-1">
                👋 Selamat Datang, {{ Auth::user()->nama_admin ?? 'Administrator' }}!
            </h4>
            <p class="text-muted mb-0" style="font-size: 0.92rem; line-height: 1.5;">
                Sistem ini membantu Pemerintah Desa Palalang menentukan warga miskin/rentan yang <strong>paling mendesak didampingi dalam pengajuan perubahan peringkat / koreksi data DTKS (PKH & BPNT)</strong>.
            </p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <a href="{{ route('laporan.cetak') }}" target="_blank" class="btn rounded-pill px-4 py-2.5 fw-semibold shadow-sm text-white" style="background-color: #f43f5e; border: none;">
                <i class="bi bi-printer-fill me-1"></i> Cetak Usulan Resmi
            </a>
        </div>
    </div>
</div>

<!-- 3 Langkah Utama (Alur Penggunaan Sistem) -->
<h6 class="fw-bold text-dark mb-3"><i class="bi bi-compass text-primary me-2"></i>3 Langkah Sederhana Menentukan Prioritas Bantuan:</h6>
<div class="row g-3 mb-4">
    <!-- Langkah 1: Data Warga -->
    <div class="col-md-4">
        <div class="card-custom p-3.5 h-100 d-flex flex-column justify-content-between border-top border-4" style="border-top-color: #38bdf8 !important;">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge rounded-circle px-2 py-1" style="background-color: #e0f2fe; color: #0284c7;">1</span>
                    <h6 class="fw-bold text-dark mb-0">Kelola Data Warga</h6>
                </div>
                <p class="text-muted" style="font-size: 0.85rem;">
                    Saat ini ada <strong>{{ $totalWarga }} keluarga</strong> terdata beserta kondisi fakta ekonominya (penghasilan riil, kondisi rumah, tanggungan).
                </p>
            </div>
            <a href="{{ route('warga.index') }}" class="btn btn-sm rounded-pill w-100 fw-semibold mt-2" style="background-color: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd;">
                Buka Data Warga <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>

    <!-- Langkah 2: Atur Prioritas Kriteria -->
    <div class="col-md-4">
        <div class="card-custom p-3.5 h-100 d-flex flex-column justify-content-between border-top border-4" style="border-top-color: #34d399 !important;">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge rounded-circle px-2 py-1" style="background-color: #ecfdf5; color: #059669;">2</span>
                    <h6 class="fw-bold text-dark mb-0">Atur Kriteria & Bobot</h6>
                </div>
                <p class="text-muted" style="font-size: 0.85rem;">
                    Tentukan kriteria apa yang paling diutamakan oleh kebijakan desa (misal: memprioritaskan penghasilan terendah & anak terbanyak).
                </p>
            </div>
            <a href="{{ route('bwm.index') }}" class="btn btn-sm rounded-pill w-100 fw-semibold mt-2" style="background-color: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;">
                Atur Bobot Kriteria <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>

    <!-- Langkah 3: Hasil & Cetak -->
    <div class="col-md-4">
        <div class="card-custom p-3.5 h-100 d-flex flex-column justify-content-between border-top border-4" style="border-top-color: #fbbf24 !important;">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge rounded-circle px-2 py-1" style="background-color: #fef3c7; color: #b45309;">3</span>
                    <h6 class="fw-bold text-dark mb-0">Hasil & Cetak Surat</h6>
                </div>
                <p class="text-muted" style="font-size: 0.85rem;">
                    Sistem otomatis menghitung urutan warga. Kuota bantuan desa saat ini: <strong>{{ $kuota }} keluarga prioritas</strong>.
                </p>
            </div>
            <a href="{{ route('marcos.index') }}" class="btn btn-sm rounded-pill w-100 fw-semibold mt-2" style="background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a;">
                Lihat Hasil Perangkingan <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</div>

<!-- Ringkasan Hasil & 5 Keluarga Prioritas -->
<div class="row g-3">
    <!-- Donut Status Kelayakan -->
    <div class="col-lg-4">
        <div class="card-custom h-100">
            <div class="card-header">
                <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-pie-chart-fill me-2" style="color: #38bdf8;"></i>Status Kelayakan Keluarga</h6>
            </div>
            <div class="card-body text-center d-flex flex-column align-items-center justify-content-center">
                <div style="width: 100%; max-width: 210px; height: 190px;">
                    <canvas id="kerentananDonutChart"></canvas>
                </div>
                <div class="w-100 mt-3 pt-2 border-top">
                    <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 0.85rem;">
                        <span><i class="bi bi-circle-fill me-1" style="color: #fb7185;"></i> Prioritas Utama (Kuota):</span>
                        <strong style="color: #e11d48;">{{ $kategori1 }} KK</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 0.85rem;">
                        <span><i class="bi bi-circle-fill me-1" style="color: #fcd34d;"></i> Prioritas Ke-2:</span>
                        <strong style="color: #d97706;">{{ $kategori2 }} KK</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center" style="font-size: 0.85rem;">
                        <span><i class="bi bi-circle-fill me-1" style="color: #cbd5e1;"></i> Daftar Cadangan:</span>
                        <strong class="text-secondary">{{ $kategori3 }} KK</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel 5 Keluarga Teratas -->
    <div class="col-lg-8">
        <div class="card-custom h-100">
            <div class="card-header">
                <div>
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-trophy-fill me-2" style="color: #fbbf24;"></i>5 Keluarga Prioritas Tertinggi</h6>
                    <small class="text-muted">Keluarga yang paling mendesak diusulkan perubahan peringkatnya</small>
                </div>
                <a href="{{ route('marcos.index') }}" class="btn btn-sm rounded-pill px-3" style="background-color: #f1f5f9; color: #334155; border: 1px solid #e2e8f0;">
                    Lihat Semua ({{ $totalHasil }}) <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted" style="font-size: 0.78rem;">
                        <tr>
                            <th class="text-center" style="width: 70px;">Urutan</th>
                            <th>Nama Kepala Keluarga</th>
                            <th>Wilayah Dusun</th>
                            <th class="text-center">Status Kelayakan</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.88rem;">
                        @forelse($topWarga as $item)
                            <tr>
                                <td class="text-center">
                                    <span class="badge border rounded-circle p-2 px-2.5 fw-bold" style="background-color: #f8fafc; color: #334155;">
                                        {{ $item->peringkat }}
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-dark d-block">{{ $item->warga->nama_kk ?? 'N/A' }}</strong>
                                    <small class="text-muted">NIK: {{ $item->warga->nik ?? '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge border" style="background-color: #f8fafc; color: #475569;">
                                        {{ $item->warga->dusun ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill px-3 py-1.5 fw-semibold" style="background-color: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;">
                                        <i class="bi bi-check-circle-fill me-1"></i> Prioritas Utama
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    Belum ada data hasil perhitungan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const ctxDonut = document.getElementById('kerentananDonutChart').getContext('2d');
        new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: ['Prioritas Utama', 'Prioritas Ke-2', 'Cadangan'],
                datasets: [{
                    data: [{{ $kategori1 }}, {{ $kategori2 }}, {{ $kategori3 }}],
                    backgroundColor: ['#fb7185', '#fcd34d', '#cbd5e1'], // Soft Coral, Soft Amber, Soft Slate
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                cutout: '72%'
            }
        });
    });
</script>
@endpush
