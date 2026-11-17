@extends('layouts.app')

@section('title', 'Data Warga & Nilai')
@section('page_title', 'Data Kepala Keluarga & Kondisi Ekonomi')

@section('content')
<div class="row g-3">
    <div class="col-12">

        <!-- Tips Singkat -->
        <div class="card-custom p-3 bg-light border d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <strong class="text-dark"><i class="bi bi-info-circle-fill text-primary me-2"></i>Petunjuk:</strong>
                <span class="text-muted" style="font-size: 0.88rem;">
                    Anda dapat menambah keluarga baru atau mengklik tombol <strong>Ubah</strong> untuk memperbarui data identitas maupun nilai kondisi ekonomi warga.
                </span>
            </div>
            <button class="btn btn-primary rounded-pill px-3.5 py-2 fw-semibold shadow-sm text-nowrap" data-bs-toggle="modal" data-bs-target="#modalTambahWarga">
                <i class="bi bi-person-plus-fill me-1"></i> Tambah Warga Baru
            </button>
        </div>

        <div class="card-custom">
            <div class="card-header">
                <div>
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-people-fill text-primary me-2"></i>Daftar Kepala Keluarga (KK) Desa Palalang</h6>
                    <small class="text-muted">Total: <strong>{{ $warga->count() }} keluarga terdaftar</strong></small>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Dusun -->
                <div class="row mb-3 align-items-center">
                    <div class="col-md-4">
                        <form method="GET" action="{{ route('warga.index') }}">
                            <select name="dusun" class="form-select form-select-sm rounded-pill" onchange="this.form.submit()">
                                <option value="">-- Tampilkan Semua Dusun --</option>
                                @foreach($dusunList as $d)
                                    <option value="{{ $d }}" {{ request('dusun') == $d ? 'selected' : '' }}>{{ $d }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 datatable-init">
                        <thead class="table-light text-muted" style="font-size: 0.78rem; text-transform: uppercase;">
                            <tr>
                                <th style="width: 50px;" class="text-center">No</th>
                                <th>Kepala Keluarga</th>
                                <th>NIK</th>
                                <th>Wilayah Dusun</th>
                                <th>Ringkasan Kondisi Ekonomi</th>
                                <th class="text-center" style="width: 130px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 0.88rem;">
                            @forelse($warga as $idx => $item)
                                @php
                                    $penghasilan = $item->getNilaiKriteria(1) ?? $item->penilaian->firstWhere('id_kriteria', 1)?->nilai_riil;
                                    $tanggungan = $item->getNilaiKriteria(2) ?? $item->penilaian->firstWhere('id_kriteria', 2)?->nilai_riil;
                                @endphp
                                <tr>
                                    <td class="text-center text-muted">{{ $idx + 1 }}</td>
                                    <td>
                                        <strong class="text-dark d-block">{{ $item->nama_kk }}</strong>
                                        <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $item->rt_rw }}</small>
                                    </td>
                                    <td>
                                        <code class="text-dark">{{ $item->nik }}</code>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-secondary border">{{ $item->dusun }}</span>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.82rem;">
                                            @if($penghasilan)
                                                <span class="d-inline-block me-2">💰 Rp {{ number_format($penghasilan, 0, ',', '.') }}</span>
                                            @endif
                                            @if($tanggungan)
                                                <span class="d-inline-block text-muted">👨‍👩‍👧‍👦 {{ $tanggungan }} Tanggungan</span>
                                            @endif
                                            @if(!$penghasilan && !$tanggungan)
                                                <span class="text-warning"><i class="bi bi-clock me-1"></i>Belum dinilai</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary" 
                                                    onclick="openEditModal({{ $item->id_warga }})" 
                                                    title="Ubah Data & Nilai Warga">
                                                <i class="bi bi-pencil-square me-1"></i> Ubah
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="confirmDeleteWarga({{ $item->id_warga }}, '{{ addslashes($item->nama_kk) }}')" 
                                                    title="Hapus Warga">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>

                                        <form id="delete-warga-{{ $item->id_warga }}" 
                                              action="{{ route('warga.destroy', $item->id_warga) }}" 
                                              method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada data warga terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Warga Baru -->
<div class="modal fade" id="modalTambahWarga" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom px-4 pt-4">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="bi bi-person-plus-fill text-primary me-2"></i>Tambah Kepala Keluarga (KK) Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('warga.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4 py-3">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">1. Identitas Kepala Keluarga</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Nomor Induk Kependudukan (NIK)</label>
                            <input type="text" name="nik" class="form-control" maxlength="16" placeholder="16 digit angka NIK" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Nama Kepala Keluarga</label>
                            <input type="text" name="nama_kk" class="form-control" placeholder="Nama lengkap sesuai KTP" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Wilayah Dusun</label>
                            <input type="text" name="dusun" class="form-control" placeholder="Contoh: Dusun Palalang Barat" list="listDusun" required>
                            <datalist id="listDusun">
                                @foreach($dusunList as $d)
                                    <option value="{{ $d }}"></option>
                                @endforeach
                            </datalist>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">RT / RW</label>
                            <input type="text" name="rt_rw" class="form-control" placeholder="Contoh: RT 001 / RW 002" required>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">2. Nilai Kondisi Ekonomi Keluarga</h6>
                    <div class="row g-3">
                        @foreach($kriteria as $c)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.85rem;">
                                    {{ $c->nama_kriteria }}
                                </label>
                                <input type="number" step="any" name="nilai[{{ $c->id_kriteria }}]" class="form-control" placeholder="Masukkan nilai angka" required>
                                <small class="text-muted" style="font-size: 0.73rem;">{{ $c->keterangan }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Warga</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Warga & Nilai Sekaligus -->
<div class="modal fade" id="modalEditWarga" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom px-4 pt-4">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="bi bi-pencil-square text-primary me-2"></i>Ubah Data & Nilai Warga
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditWarga" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body px-4 py-3">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">1. Identitas Kepala Keluarga</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">NIK</label>
                            <input type="text" name="nik" id="edit_nik" class="form-control" maxlength="16" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Nama Kepala Keluarga</label>
                            <input type="text" name="nama_kk" id="edit_nama_kk" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Wilayah Dusun</label>
                            <input type="text" name="dusun" id="edit_dusun" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">RT / RW</label>
                            <input type="text" name="rt_rw" id="edit_rt_rw" class="form-control" required>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">2. Nilai Kondisi Ekonomi Keluarga</h6>
                    <div class="row g-3">
                        @foreach($kriteria as $c)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.85rem;">
                                    {{ $c->nama_kriteria }}
                                </label>
                                <input type="number" step="any" name="nilai[{{ $c->id_kriteria }}]" id="edit_nilai_{{ $c->id_kriteria }}" class="form-control" required>
                                <small class="text-muted" style="font-size: 0.72rem;">{{ $c->keterangan }}</small>
                            </div>
                        @endforeach
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
@endsection

@push('scripts')
<script>
    function openEditModal(id) {
        Swal.fire({
            title: 'Memuat...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: `{{ url('/warga') }}/${id}/detail`,
            method: 'GET',
            success: function(res) {
                Swal.close();
                const warga = res.warga;
                const penilaian = res.penilaian;

                $('#formEditWarga').attr('action', `{{ url('/warga') }}/${id}`);
                $('#edit_nik').val(warga.nik);
                $('#edit_nama_kk').val(warga.nama_kk);
                $('#edit_dusun').val(warga.dusun);
                $('#edit_rt_rw').val(warga.rt_rw);

                for (const [cId, val] of Object.entries(penilaian)) {
                    $(`#edit_nilai_${cId}`).val(val);
                }

                $('#modalEditWarga').modal('show');
            },
            error: function() {
                Swal.fire('Error', 'Gagal memuat data warga.', 'error');
            }
        });
    }

    function confirmDeleteWarga(id, nama) {
        Swal.fire({
            title: `Hapus KK ${nama}?`,
            text: "Data warga dan seluruh nilainya akan dihapus.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-warga-${id}`).submit();
            }
        });
    }
</script>
@endpush
