@extends('layouts.app')

@section('title', 'Konfigurasi Profil Desa & Kop Surat')
@section('page_title', 'Konfigurasi Profil Desa & Kop Surat Resmi')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-10">
        <div class="card-custom">
            <div class="card-header">
                <div>
                    <h6 class="mb-0 fw-bold"><i class="bi bi-gear-fill text-primary me-2"></i>Pengaturan Instansi & Format Kop Surat</h6>
                    <small class="text-muted">Konfigurasi ini akan otomatis ditampilkan pada Kop Surat dan Lembar Rekomendasi Resmi</small>
                </div>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('pengaturan.update') }}" method="POST">
                    @csrf
                    
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">1. Data Wilayah Pemerintahan</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Nama Desa</label>
                            <input type="text" name="nama_desa" class="form-control" value="{{ old('nama_desa', $setting->nama_desa) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Kecamatan</label>
                            <input type="text" name="kecamatan" class="form-control" value="{{ old('kecamatan', $setting->kecamatan) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Kabupaten</label>
                            <input type="text" name="kabupaten" class="form-control" value="{{ old('kabupaten', $setting->kabupaten) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Provinsi</label>
                            <input type="text" name="provinsi" class="form-control" value="{{ old('provinsi', $setting->provinsi) }}" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Alamat Kantor Balai Desa</label>
                            <input type="text" name="alamat_kantor" class="form-control" value="{{ old('alamat_kantor', $setting->alamat_kantor) }}" placeholder="Jl. Balai Desa No. 01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Kode Pos</label>
                            <input type="text" name="kode_pos" class="form-control" value="{{ old('kode_pos', $setting->kode_pos) }}" placeholder="69352">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Email Resmi Desa</label>
                            <input type="email" name="email_desa" class="form-control" value="{{ old('email_desa', $setting->email_desa) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">No. Kontak / Telepon Balai Desa</label>
                            <input type="text" name="telepon_desa" class="form-control" value="{{ old('telepon_desa', $setting->telepon_desa) }}">
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">2. Pejabat Penandatangan Laporan</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Nama Kepala Desa (Kades)</label>
                            <input type="text" name="nama_kades" class="form-control" value="{{ old('nama_kades', $setting->nama_kades) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">NIP Kepala Desa</label>
                            <input type="text" name="nip_kades" class="form-control" value="{{ old('nip_kades', $setting->nip_kades) }}" placeholder="19xxxxxxxx xxxxxx x xxx">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Nama Ketua BPD</label>
                            <input type="text" name="nama_bpd" class="form-control" value="{{ old('nama_bpd', $setting->nama_bpd) }}" placeholder="Ketua Badan Permusyawaratan Desa">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Kuota Default Bantuan (Jumlah KK)</label>
                            <input type="number" min="1" name="kuota_bantuan" class="form-control" value="{{ old('kuota_bantuan', $setting->kuota_bantuan) }}" required>
                            <small class="text-muted" style="font-size: 0.72rem;">Jumlah calon penerima yang akan diberi status "Prioritas Utama"</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end pt-3 border-top">
                        <button type="submit" class="btn btn-primary rounded-pill px-5 py-2.5 shadow-sm fw-bold">
                            <i class="bi bi-save me-1"></i> Simpan Konfigurasi Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

