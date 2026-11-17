@extends('layouts.app')

@section('title', 'Analisis MARCOS')
@section('page_title', 'Analisis MARCOS')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 text-center py-5">
        <div class="card-custom p-5">
            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex p-4 mb-3">
                <i class="bi bi-diagram-3 fs-1"></i>
            </div>
            <h4 class="fw-bold text-dark">Data Belum Lengkap</h4>
            <p class="text-muted">
                Untuk menjalankan analisis MARCOS, Anda perlu melengkapi data <strong>Kriteria</strong> dan data <strong>Warga (Alternatif)</strong> terlebih dahulu.
            </p>
            <div class="d-flex justify-content-center gap-2 mt-3">
                <a href="{{ route('kriteria.index') }}" class="btn btn-outline-primary rounded-pill px-4">
                    <i class="bi bi-sliders me-1"></i> Kelola Kriteria
                </a>
                <a href="{{ route('warga.index') }}" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-person-plus me-1"></i> Kelola Warga
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

