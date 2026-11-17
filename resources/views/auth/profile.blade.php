@extends('layouts.app')

@section('title', 'Profil Administrator')
@section('page_title', 'Profil Administrator Sistem')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-7 col-lg-9">
        <div class="card-custom">
            <div class="card-header">
                <div>
                    <h6 class="mb-0 fw-bold"><i class="bi bi-person-badge-fill text-primary me-2"></i>Kelola Akun Administrator</h6>
                    <small class="text-muted">Perbarui identitas akun dan keamanan kata sandi</small>
                </div>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Nama Lengkap Administrator</label>
                        <input type="text" name="nama_admin" class="form-control" value="{{ old('nama_admin', $user->nama_admin) }}" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Username Login</label>
                            <input type="text" name="username" class="form-control" value="{{ old('username', $user->username) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Alamat Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}">
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3 border mb-4">
                        <h6 class="fw-bold text-dark mb-2" style="font-size: 0.88rem;">
                            <i class="bi bi-shield-lock text-primary me-1"></i> Ubah Kata Sandi (Opsional)
                        </h6>
                        <small class="text-muted d-block mb-3" style="font-size: 0.78rem;">
                            Kosongkan bagian ini jika Anda tidak ingin mengubah kata sandi.
                        </small>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark" style="font-size: 0.82rem;">Kata Sandi Saat Ini</label>
                            <input type="password" name="current_password" class="form-control" placeholder="Masukkan password sekarang">
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark" style="font-size: 0.82rem;">Kata Sandi Baru</label>
                                <input type="password" name="new_password" class="form-control" placeholder="Minimal 6 karakter">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark" style="font-size: 0.82rem;">Konfirmasi Kata Sandi Baru</label>
                                <input type="password" name="new_password_confirmation" class="form-control" placeholder="Ulangi kata sandi baru">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary rounded-pill px-5 py-2.5 shadow-sm fw-bold">
                            <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

