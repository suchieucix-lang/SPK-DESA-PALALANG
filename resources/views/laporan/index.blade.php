@extends('layouts.app')

@section('title', 'Daftar Usulan Bantuan')
@section('page_title', 'Daftar Usulan & Rekomendasi Bantuan Sosial')

@section('content')
<div class="row g-3">
    <div class="col-12">

        <!-- Guide Banner -->
        <div class="guide-banner shadow-sm">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-start gap-3">
                    <i class="bi bi-file-earmark-check-fill text-success fs-3 mt-1"></i>
                    <div>
                        <strong class="text-dark d-block">Dokumen Usulan Bantuan Sosial Siap Diterbitkan:</strong>
                        <p class="text-muted mb-0" style="font-size: 0.84rem;">
                            Daftar ini adalah hasil akhir perangkingan BWM-MARCOS yang dapat langsung dicetak ber-Kop Surat resmi Desa Palalang untuk ditandatangani Kepala Desa dan Ketua BPD.
                        </p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('laporan.export', request()->all()) }}" class="btn btn-outline-success rounded-pill px-3 shadow-sm">
                        <i class="bi bi-file-earmark-excel me-1"></i> Unduh Excel
                    </a>
                    <a href="{{ route('laporan.cetak', request()->all()) }}" target="_blank" class="btn btn-danger rounded-pill px-3.5 shadow-sm fw-bold">
                        <i class="bi bi-printer-fill me-1"></i> Cetak Surat Resmi
                    </a>
                </div>
            </div>
        </div>

        <div class="card-custom">
            <div class="card-header">
                <div>
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-list-ol text-primary me-2"></i>Daftar Calon Penerima Bantuan Sosial</h6>
                    <small class="text-muted">Berdasarkan Kuota: <strong>{{ $kuota }} KK Penerima</strong></small>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Section -->
                <form method="GET" action="{{ route('laporan.index') }}" class="row g-2 mb-3 align-items-end p-2.5 bg-light rounded-3 border">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.78rem;">Pilih Dusun:</label>
                        <select name="dusun" class="form-select form-select-sm rounded-pill" onchange="this.form.submit()">
                            <option value="all">-- Semua Dusun --</option>
                            @foreach($dusunList as $d)
                                <option value="{{ $d }}" {{ request('dusun') == $d ? 'selected' : '' }}>{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.78rem;">Kelompok Penerima:</label>
                        <select name="filter_status" class="form-select form-select-sm rounded-pill" onchange="this.form.submit()">
                            <option value="all" {{ request('filter_status') == 'all' ? 'selected' : '' }}>Semua Peringkat</option>
                            <option value="prioritas" {{ request('filter_status') == 'prioritas' ? 'selected' : '' }}>Hanya Lolos Kuota (Top {{ $kuota }})</option>
                            <option value="cadangan" {{ request('filter_status') == 'cadangan' ? 'selected' : '' }}>Hanya Cadangan</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.78rem;">Jumlah Kuota Bantuan:</label>
                        <input type="number" min="1" name="kuota" value="{{ $kuota }}" class="form-control form-control-sm rounded-pill">
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 w-100">
                            Terapkan Filter
                        </button>
                        <a href="{{ route('laporan.index') }}" class="btn btn-sm btn-light border rounded-pill">Reset</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 datatable-init">
                        <thead class="table-light text-muted" style="font-size: 0.78rem; text-transform: uppercase;">
                            <tr>
                                <th style="width: 70px;" class="text-center">Urutan</th>
                                <th>Nama Kepala Keluarga</th>
                                <th>NIK</th>
                                <th>Dusun</th>
                                <th>RT / RW</th>
                                <th class="text-center">Skor Kelayakan</th>
                                <th class="text-center">Status Keputusan</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 0.88rem;">
                            @forelse($hasil as $h)
                                @php
                                    $isLolos = $h->peringkat <= $kuota;
                                @endphp
                                <tr class="{{ $isLolos ? 'table-light' : '' }}">
                                    <td class="text-center">
                                        @if($h->peringkat <= 3)
                                            <span class="badge bg-warning text-dark rounded-circle p-2 px-2.5 fw-bold">{{ $h->peringkat }}</span>
                                        @else
                                            <span class="badge bg-light text-dark border rounded-circle p-2 px-2.5 fw-bold">{{ $h->peringkat }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong class="text-dark">{{ $h->warga->nama_kk ?? '-' }}</strong>
                                    </td>
                                    <td>
                                        <code class="text-dark">{{ $h->warga->nik ?? '-' }}</code>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-secondary border">{{ $h->warga->dusun ?? '-' }}</span>
                                    </td>
                                    <td class="text-muted">{{ $h->warga->rt_rw ?? '-' }}</td>
                                    <td class="text-center fw-bold text-primary">
                                        {{ number_format($h->skor_utilitas, 4) }}
                                    </td>
                                    <td class="text-center">
                                        @if($isLolos)
                                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-1.5 rounded-pill fw-semibold">
                                                <i class="bi bi-check-circle-fill me-1"></i> Penerima Bantuan (Kuota)
                                            </span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-1.5 rounded-pill fw-semibold">
                                                <i class="bi bi-clock me-1"></i> Cadangan
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        Tidak ada data yang memenuhi kriteria filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
