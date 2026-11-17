@extends('layouts.app')

@section('title', 'Kriteria Penilaian')
@section('page_title', 'Master Data Kriteria Penilaian')

@section('content')
<div class="row g-3">
    <div class="col-12">

        <!-- Tips Penjelasan Jenis Kriteria -->
        <div class="guide-banner shadow-sm">
            <div class="d-flex align-items-start gap-3">
                <i class="bi bi-info-circle-fill text-primary fs-4 mt-1"></i>
                <div>
                    <strong class="text-dark d-block mb-1">Panduan Memahami Sifat Kriteria:</strong>
                    <div class="row g-2 text-muted" style="font-size: 0.83rem;">
                        <div class="col-md-6">
                            <span class="badge bg-warning bg-opacity-25 text-dark fw-bold me-1">Cost (Beban)</span>
                            Makin <strong>rendah</strong> nilainya, makin <strong>layak</strong> menerima bantuan (Contoh: Penghasilan bulanan yang rendah, aset sedikit).
                        </div>
                        <div class="col-md-6">
                            <span class="badge bg-success bg-opacity-25 text-success fw-bold me-1">Benefit (Keuntungan)</span>
                            Makin <strong>tinggi</strong> nilainya, makin <strong>layak</strong> menerima bantuan (Contoh: Jumlah anggota keluarga yang ditanggung).
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-custom">
            <div class="card-header">
                <div>
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-list-check text-primary me-2"></i>Daftar Parameter Kriteria</h6>
                    <small class="text-muted">Total Bobot: <strong>{{ number_format($totalBobot * 100, 1) }}%</strong></small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('bwm.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        <i class="bi bi-sliders me-1"></i> Atur Bobot (BWM)
                    </a>
                    <button class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalTambahKriteria">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Kriteria
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 datatable-init">
                        <thead class="table-light text-muted" style="font-size: 0.78rem; text-transform: uppercase;">
                            <tr>
                                <th style="width: 70px;" class="text-center">Kode</th>
                                <th>Nama Kriteria</th>
                                <th class="text-center" style="width: 140px;">Sifat Kriteria</th>
                                <th class="text-center" style="width: 130px;">Bobot Saat Ini</th>
                                <th>Keterangan Pengukuran</th>
                                <th class="text-center" style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 0.88rem;">
                            @forelse($kriteria as $item)
                                <tr>
                                    <td class="text-center">
                                        <span class="badge bg-dark px-2.5 py-1.5">{{ $item->kode }}</span>
                                    </td>
                                    <td>
                                        <strong class="text-dark">{{ $item->nama_kriteria }}</strong>
                                    </td>
                                    <td class="text-center">
                                        @if($item->jenis === 'benefit')
                                            <span class="badge bg-success bg-opacity-10 text-success px-2.5 py-1 rounded-pill">
                                                <i class="bi bi-arrow-up-right me-1"></i> Benefit
                                            </span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-dark px-2.5 py-1 rounded-pill">
                                                <i class="bi bi-arrow-down-right me-1"></i> Cost
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="fw-bold text-primary">{{ number_format($item->bobot * 100, 1) }}%</div>
                                        <small class="text-muted" style="font-size: 0.72rem;">Nilai: {{ number_format($item->bobot, 4) }}</small>
                                    </td>
                                    <td class="text-muted" style="font-size: 0.82rem;">
                                        {{ $item->keterangan ?? '-' }}
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-secondary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalEditKriteria{{ $item->id_kriteria }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="confirmDelete('{{ $item->id_kriteria }}', '{{ $item->kode }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>

                                        <form id="delete-form-{{ $item->id_kriteria }}" 
                                              action="{{ route('kriteria.destroy', $item->id_kriteria) }}" 
                                              method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>

                                <!-- Modal Edit Kriteria -->
                                <div class="modal fade" id="modalEditKriteria{{ $item->id_kriteria }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg rounded-4">
                                            <div class="modal-header border-bottom px-4 pt-4">
                                                <h5 class="modal-title fw-bold text-dark">
                                                    Edit Kriteria {{ $item->kode }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('kriteria.update', $item->id_kriteria) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body px-4 py-3">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Kode Kriteria</label>
                                                        <input type="text" name="kode" class="form-control" value="{{ old('kode', $item->kode) }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Nama Kriteria</label>
                                                        <input type="text" name="nama_kriteria" class="form-control" value="{{ old('nama_kriteria', $item->nama_kriteria) }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Sifat Kriteria</label>
                                                        <select name="jenis" class="form-select" required>
                                                            <option value="cost" {{ $item->jenis === 'cost' ? 'selected' : '' }}>Cost (Makin kecil nilainya = Makin layak dibantu)</option>
                                                            <option value="benefit" {{ $item->jenis === 'benefit' ? 'selected' : '' }}>Benefit (Makin besar nilainya = Makin layak dibantu)</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Keterangan / Panduan Isi Nilai</label>
                                                        <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $item->keterangan) }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top px-4 py-3">
                                                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada data kriteria.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Kriteria -->
<div class="modal fade" id="modalTambahKriteria" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom px-4 pt-4">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="bi bi-plus-circle-fill text-primary me-2"></i>Tambah Kriteria Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('kriteria.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Kode Kriteria</label>
                        <input type="text" name="kode" class="form-control" placeholder="Contoh: C6" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Nama Kriteria</label>
                        <input type="text" name="nama_kriteria" class="form-control" placeholder="Contoh: Kondisi Kesehatan Keluarga" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Sifat Kriteria</label>
                        <select name="jenis" class="form-select" required>
                            <option value="cost" selected>Cost (Makin kecil nilainya = Makin diprioritaskan)</option>
                            <option value="benefit">Benefit (Makin besar nilainya = Makin diprioritaskan)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Keterangan / Panduan Nilai</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Jelaskan tolok ukur pengisian nilai"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Kriteria</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(id, kode) {
        Swal.fire({
            title: `Hapus Kriteria ${kode}?`,
            text: "Penghapusan kriteria akan mempengaruhi data penilaian yang sudah diisi.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    }
</script>
@endpush
