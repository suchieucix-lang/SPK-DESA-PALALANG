@extends('layouts.app')

@section('title', 'Panduan & Latar Belakang Masalah')
@section('page_title', 'Latar Belakang Masalah & Cara Kerja Sistem')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-10 col-lg-11">

        <!-- Banner Konteks Nyata Masalah -->
        <div class="card-custom p-4 mb-4" style="background: linear-gradient(135deg, #0284c7 0%, #0f766e 100%); color: #ffffff;">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-white text-primary p-3 d-inline-flex fs-3">
                    <i class="bi bi-shield-exclamation"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1">Masalah Nyata di Desa Palalang: Koreksi DTKS/DTSEN (PKH & BPNT)</h4>
                    <p class="mb-0 text-white-50" style="font-size: 0.92rem;">
                        Bantuan PKH dan BPNT berbasis data nasional. Sistem ini menjadi <strong>dasar penilaian objektif</strong> bagi Pemerintah Desa Palalang untuk menentukan siapa warga yang <strong>paling mendesak didampingi dalam pengajuan perubahan peringkat / koreksi data DTKS</strong>.
                    </p>
                </div>
            </div>
        </div>

        <!-- 1. Bedah Masalah Riil Desa Palalang -->
        <div class="card-custom mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>1. Akar Masalah: Mengapa Sistem Ini Dibuat?</h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 border h-100">
                            <span class="badge bg-danger mb-2">Fakta 1: Data Salah Catat</span>
                            <strong class="text-dark d-block mb-1">Data Nasional (DTKS/DTSEN) Tidak Akurat</strong>
                            <p class="text-muted mb-0" style="font-size: 0.82rem;">
                                Penerima PKH dan BPNT ditarik dari data pusat. Faktanya di Desa Palalang, ada keluarga yang kondisi riilnya miskin/rentan, tetapi di data pusat tercatat sebagai golongan mampu sehingga <strong>tidak menerima bantuan</strong> (*Exclusion Error*).
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 border h-100">
                            <span class="badge bg-warning text-dark mb-2">Fakta 2: Hambatan Digital</span>
                            <strong class="text-dark d-block mb-1">Warga Rentan Belum Paham Aplikasi</strong>
                            <p class="text-muted mb-0" style="font-size: 0.82rem;">
                                Pengajuan sanggah/perubahan peringkat sebenarnya ada secara digital (seperti aplikasi Cek Bansos Kemensos). Namun, warga rentan umumnya tidak punya smartphone, lansia, atau <strong>tidak paham cara memakainya</strong>.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 border h-100">
                            <span class="badge bg-primary mb-2">Fakta 3: Desa Bingung Memilih</span>
                            <strong class="text-dark d-block mb-1">Desa Belum Punya Dasar Terukur</strong>
                            <p class="text-muted mb-0" style="font-size: 0.82rem;">
                                Perangkat Desa ingin mendampingi warga untuk mengusulkan koreksi data ke Kemensos / Musdes, tetapi desa belum memiliki <strong>dasar penilaian terukur</strong> untuk menentukan keluarga mana yang harus diprioritaskan lebih dulu.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Solusi yang Diberikan Aplikasi Ini -->
        <div class="card-custom mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-check2-circle text-success me-2"></i>2. Bagaimana Sistem Ini Menjawab Masalah Tersebut?</h6>
            </div>
            <div class="card-body p-4">
                <p class="text-muted mb-3" style="font-size: 0.88rem;">
                    Sistem Pendukung Keputusan (SPK) ini hadir sebagai <strong>alat ukur resmi Pemerintah Desa Palalang</strong> melalui 3 tahap konkret:
                </p>
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex align-items-start gap-3 p-3 rounded-3 bg-light border">
                        <span class="badge bg-primary rounded-circle p-2 px-2.5 fs-6">A</span>
                        <div>
                            <strong class="text-dark d-block">Verifikasi Fakta Lapangan (Bukan Mengira-ngira)</strong>
                            <small class="text-muted">Perangkat desa menginput kondisi nyata warga: penghasilan riil, jumlah tanggungan anak sekolah/lansia, kondisi fisik rumah, dan aset produktif.</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-3 p-3 rounded-3 bg-light border">
                        <span class="badge bg-primary rounded-circle p-2 px-2.5 fs-6">B</span>
                        <div>
                            <strong class="text-dark d-block">Pembobotan Ilmiah Objektif (Metode BWM)</strong>
                            <small class="text-muted">Metode BWM memastikan faktor yang paling menentukan kemiskinan (seperti penghasilan terendah & tanggungan terbanyak) memiliki pengaruh persentase terbesar secara adil.</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-3 p-3 rounded-3 bg-light border">
                        <span class="badge bg-success rounded-circle p-2 px-2.5 fs-6">C</span>
                        <div>
                            <strong class="text-dark d-block">Perangkingan Urutan Prioritas Pendampingan (Metode MARCOS)</strong>
                            <small class="text-muted">Metode MARCOS membandingkan kondisi riil seluruh keluarga, lalu mengurutkan peringkat: <strong>Warga mana yang paling parah salah catatnya dan paling mendesak didampingi untuk diusulkan koreksinya ke DTKS</strong>.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Alur Nyata dari Komputer ke Lapangan -->
        <div class="card-custom mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-arrow-repeat text-primary me-2"></i>3. Alur Praktis dari Komputer Sampai ke Bansos PKH/BPNT</h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3 align-items-center">
                    <div class="col-md-3 text-center">
                        <div class="p-3 border rounded-3 bg-white">
                            <i class="bi bi-person-fill-gear fs-2 text-primary d-block mb-1"></i>
                            <strong class="d-block" style="font-size: 0.85rem;">1. Input Data Riil</strong>
                            <small class="text-muted">Desa input kondisi fisik & ekonomi keluarga</small>
                        </div>
                    </div>
                    <div class="col-md-1 text-center d-none d-md-block">
                        <i class="bi bi-arrow-right fs-3 text-muted"></i>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="p-3 border rounded-3 bg-white border-primary">
                            <i class="bi bi-cpu fs-2 text-primary d-block mb-1"></i>
                            <strong class="d-block text-primary" style="font-size: 0.85rem;">2. BWM & MARCOS Hitung</strong>
                            <small class="text-muted">Mengeluarkan daftar peringkat prioritas pendampingan yang objektif</small>
                        </div>
                    </div>
                    <div class="col-md-1 text-center d-none d-md-block">
                        <i class="bi bi-arrow-right fs-3 text-muted"></i>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="p-3 border rounded-3 bg-white border-success">
                            <i class="bi bi-file-earmark-pdf-fill fs-2 text-success d-block mb-1"></i>
                            <strong class="d-block text-success" style="font-size: 0.85rem;">3. Cetak Surat Musdes</strong>
                            <small class="text-muted">Ditandatangani Kades untuk dibawa ke Musdes & input ke SIKS-NG</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        

    </div>
</div>
@endsection
