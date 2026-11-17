@extends('layouts.app')

@section('title', 'Isi Nilai Kondisi KK')
@section('page_title', 'Pengisian Nilai Riil Kondisi Keluarga')

@section('content')
<div class="row g-3">
    <div class="col-12">

        <!-- Guide Banner -->
        <div class="guide-banner shadow-sm">
            <div class="d-flex align-items-start gap-3">
                <i class="bi bi-lightbulb-fill text-warning fs-4 mt-1"></i>
                <div>
                    <strong class="text-dark d-block mb-1">Panduan Pengisian Nilai Warga:</strong>
                    <p class="text-muted mb-0" style="font-size: 0.83rem;">
                        Ketikkan angka kondisi riil keluarga pada kotak di bawah sesuai kriteria (misal: <strong>Penghasilan</strong> diketik nominal rupiah rata-rata, <strong>Tanggungan</strong> diketik jumlah orang, dll). Setelah selesai mengedit, klik tombol <strong>"Simpan Nilai Penilaian"</strong> di bagian bawah.
                    </p>
                </div>
            </div>
        </div>

        <div class="card-custom">
            <div class="card-header">
                <div>
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-table text-primary me-2"></i>Tabel Pengisian Nilai Keluarga</h6>
                    <small class="text-muted">Total: {{ $warga->count() }} keluarga & {{ $kriteria->count() }} kriteria</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('marcos.index') }}" class="btn btn-sm btn-primary rounded-pill px-3">
                        <i class="bi bi-trophy-fill me-1"></i> Lihat Hasil Perangkingan
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Dusun -->
                <form method="GET" action="{{ route('penilaian.index') }}" class="row g-2 mb-3 align-items-center">
                    <div class="col-md-4">
                        <select name="dusun" class="form-select form-select-sm rounded-pill" onchange="this.form.submit()">
                            <option value="">-- Tampilkan Semua Dusun --</option>
                            @foreach($dusunList as $d)
                                <option value="{{ $d }}" {{ request('dusun') == $d ? 'selected' : '' }}>{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <form action="{{ route('penilaian.batch') }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="table-light text-muted" style="font-size: 0.78rem;">
                                <tr>
                                    <th style="width: 40px;" class="text-center">No</th>
                                    <th style="min-width: 170px;">Kepala Keluarga</th>
                                    <th style="min-width: 130px;">Dusun</th>
                                    @foreach($kriteria as $c)
                                        <th class="text-center" style="min-width: 130px;">
                                            <div class="fw-bold text-dark">{{ $c->nama_kriteria }}</div>
                                            <span class="badge bg-light text-secondary border" style="font-size: 0.68rem;">
                                                {{ $c->kode }} &bull; {{ ucfirst($c->jenis) }}
                                            </span>
                                        </th>
                                    @endforeach
                                    <th style="width: 70px;" class="text-center">Edit</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 0.86rem;">
                                @forelse($warga as $idx => $w)
                                    <tr>
                                        <td class="text-center text-muted">{{ $idx + 1 }}</td>
                                        <td>
                                            <strong class="text-dark d-block">{{ $w->nama_kk }}</strong>
                                            <small class="text-muted">{{ $w->nik }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-secondary border">{{ $w->dusun }}</span>
                                        </td>
                                        @foreach($kriteria as $c)
                                            @php
                                                $val = $matrix[$w->id_warga][$c->id_kriteria] ?? '';
                                            @endphp
                                            <td class="p-1">
                                                <input type="number" step="any" 
                                                       name="nilai[{{ $w->id_warga }}][{{ $c->id_kriteria }}]" 
                                                       value="{{ $val }}" 
                                                       class="form-control form-control-sm text-center"
                                                       placeholder="0" required>
                                            </td>
                                        @endforeach
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="quickEditRow({{ $w->id_warga }}, '{{ addslashes($w->nama_kk) }}')" 
                                                    title="Edit cepat per orang via modal">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 4 + $kriteria->count() }}" class="text-center py-4 text-muted">
                                            Belum ada data warga terdaftar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($warga->isNotEmpty())
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                            <small class="text-muted">
                                Pastikan angka terisi lengkap sebelum melanjutkan ke perhitungan perangkingan.
                            </small>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm">
                                <i class="bi bi-check2-circle me-1"></i> Simpan Nilai Penilaian
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Quick Edit -->
<div class="modal fade" id="modalQuickEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom px-4 pt-4">
                <h5 class="modal-title fw-bold text-dark">
                    Edit Nilai: <span id="quickKKName" class="text-primary">-</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formQuickEdit">
                @csrf
                <input type="hidden" name="id_warga" id="quickWargaId">
                <div class="modal-body px-4 py-3">
                    <div class="row g-2.5">
                        @foreach($kriteria as $c)
                            <div class="col-12 mb-2">
                                <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.85rem;">
                                    {{ $c->nama_kriteria }} ({{ $c->kode }})
                                </label>
                                <input type="number" step="any" name="nilai[{{ $c->id_kriteria }}]" id="quick_input_{{ $c->id_kriteria }}" class="form-control" required>
                                <small class="text-muted" style="font-size: 0.72rem;">{{ $c->keterangan }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Nilai</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function quickEditRow(wargaId, namaKk) {
        $('#quickWargaId').val(wargaId);
        $('#quickKKName').text(namaKk);

        @foreach($kriteria as $c)
            const val_{{ $c->id_kriteria }} = $(`input[name="nilai[${wargaId}][{{ $c->id_kriteria }}]"]`).val();
            $(`#quick_input_{{ $c->id_kriteria }}`).val(val_{{ $c->id_kriteria }});
        @endforeach

        $('#modalQuickEdit').modal('show');
    }

    $('#formQuickEdit').on('submit', function(e) {
        e.preventDefault();
        const payload = $(this).serialize();

        Swal.fire({
            title: 'Menyimpan...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: "{{ route('penilaian.single') }}",
            method: "POST",
            data: payload,
            success: function(res) {
                Swal.close();
                $('#modalQuickEdit').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Tersimpan',
                    text: 'Nilai warga berhasil diperbarui.',
                    timer: 1200,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            },
            error: function() {
                Swal.fire('Error', 'Gagal memperbarui nilai.', 'error');
            }
        });
    });
</script>
@endpush
