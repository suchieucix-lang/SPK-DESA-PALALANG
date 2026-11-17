@extends('layouts.app')

@section('title', 'Atur Bobot Kriteria')
@section('page_title', 'Penentuan Bobot Kriteria (Metode BWM)')

@push('styles')
<style>
    /* Wizard Steps Soft */
    .wizard-steps-simple {
        display: flex;
        justify-content: center;
        gap: 1.25rem;
        margin-bottom: 2rem;
    }
    .wizard-step-pill {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.55rem 1.35rem;
        border-radius: 50px;
        background: #f1f5f9;
        color: #64748b;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid #e2e8f0;
    }
    .wizard-step-pill.active {
        background: #0284c7;
        color: #ffffff;
        border-color: #0284c7;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
    }
    .wizard-step-pill.completed {
        background: #ecfdf5;
        color: #047857;
        border-color: #a7f3d0;
    }
    .wizard-step-pill .step-num {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: rgba(0, 0, 0, 0.08);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
    }
    .wizard-step-pill.active .step-num {
        background: rgba(255, 255, 255, 0.25);
    }

    /* Slider Soft Styling */
    .bwm-slider {
        -webkit-appearance: none;
        appearance: none;
        width: 100%;
        height: 8px;
        border-radius: 4px;
        background: #e2e8f0;
        outline: none;
    }
    .bwm-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #0284c7;
        cursor: pointer;
        border: 2px solid #ffffff;
        box-shadow: 0 2px 6px rgba(2, 132, 199, 0.3);
    }
    .scale-badge-soft {
        font-size: 0.82rem;
        font-weight: 600;
        background: #f0f9ff;
        color: #0284c7;
        border: 1px solid #bae6fd;
        padding: 0.35rem 0.8rem;
        border-radius: 8px;
        display: inline-block;
    }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-10 col-lg-11">

        <!-- Wizard Navigation Pills -->
        <div class="wizard-steps-simple">
            <div class="wizard-step-pill active" id="pill-step-1" onclick="goToStep(1)">
                <span class="step-num">1</span>
                <span>Pilih Kriteria Utama</span>
            </div>
            <div class="wizard-step-pill" id="pill-step-2" onclick="goToStep(2)">
                <span class="step-num">2</span>
                <span>Bandingkan Nilai</span>
            </div>
            <div class="wizard-step-pill" id="pill-step-3" onclick="goToStep(3)">
                <span class="step-num">3</span>
                <span>Hasil & Simpan</span>
            </div>
        </div>

        <!-- LANGKAH 1 -->
        <div class="card-custom wizard-pane" id="step-pane-1">
            <div class="card-header">
                <div>
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-star-fill me-2" style="color: #fbbf24;"></i>Langkah 1: Tentukan Kriteria Paling Penting & Paling Rendah</h6>
                    <small class="text-muted">Pilih parameter acuan yang paling membuktikan bahwa seseorang itu miskin dan layak dibantu</small>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-4 mb-4">
                    <!-- Kriteria Terbaik -->
                    <div class="col-md-6">
                        <div class="p-4 rounded-4 border h-100" style="background-color: #f0f9ff; border-color: #bae6fd !important;">
                            <div class="d-flex align-items-center gap-2 mb-2" style="color: #0284c7;">
                                <i class="bi bi-arrow-up-circle-fill fs-4"></i>
                                <h6 class="fw-bold mb-0">Kriteria Paling Utama (Best)</h6>
                            </div>
                            <p class="text-muted mb-3" style="font-size: 0.83rem;">
                                Faktor apa yang <strong>paling kuat membuktikan</strong> bahwa sebuah keluarga layak didampingi koreksi DTKS?
                            </p>
                            <select id="select_best" class="form-select form-select-lg rounded-3 shadow-sm" style="border-color: #7dd3fc;">
                                @foreach($kriteria as $c)
                                    <option value="{{ $c->id_kriteria }}" {{ $c->id_kriteria == $bestId ? 'selected' : '' }}>
                                        {{ $c->kode }} - {{ $c->nama_kriteria }} ({{ $c->jenis == 'cost' ? 'Beban' : 'Keuntungan' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Kriteria Terendah -->
                    <div class="col-md-6">
                        <div class="p-4 rounded-4 border h-100" style="background-color: #fff1f2; border-color: #fecdd3 !important;">
                            <div class="d-flex align-items-center gap-2 mb-2" style="color: #e11d48;">
                                <i class="bi bi-arrow-down-circle-fill fs-4"></i>
                                <h6 class="fw-bold mb-0">Kriteria Paling Rendah Pengaruhnya (Worst)</h6>
                            </div>
                            <p class="text-muted mb-3" style="font-size: 0.83rem;">
                                Faktor apa yang pengaruhnya <strong>paling kecil</strong> dibanding kriteria lainnya?
                            </p>
                            <select id="select_worst" class="form-select form-select-lg rounded-3 shadow-sm" style="border-color: #fca5a5;">
                                @foreach($kriteria as $c)
                                    <option value="{{ $c->id_kriteria }}" {{ $c->id_kriteria == $worstId ? 'selected' : '' }}>
                                        {{ $c->kode }} - {{ $c->nama_kriteria }} ({{ $c->jenis == 'cost' ? 'Beban' : 'Keuntungan' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="button" class="btn rounded-pill px-4 py-2.5 fw-semibold text-white shadow-sm" style="background-color: #0284c7; border: none;" onclick="goToStep(2)">
                        Lanjut ke Perbandingan Kriteria <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- LANGKAH 2 -->
        <div class="card-custom wizard-pane d-none" id="step-pane-2">
            <div class="card-header">
                <div>
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-sliders me-2" style="color: #0284c7;"></i>Langkah 2: Geser Slider Tingkat Kepentingan</h6>
                    <small class="text-muted">Tentukan seberapa lebih penting kriteria utama dibanding kriteria lainnya</small>
                </div>
            </div>
            <div class="card-body p-4">
                <input type="hidden" id="engineSelector" value="php">

                <!-- Ringkasan Pilihan -->
                <div class="p-3 rounded-3 border mb-3 d-flex justify-content-between align-items-center" style="background-color: #f8fafc; font-size: 0.85rem;">
                    <div>
                        <span class="text-muted">Kriteria Paling Utama:</span>
                        <strong class="ms-1" style="color: #0284c7;" id="label_best_name">-</strong>
                    </div>
                    <div>
                        <span class="text-muted">Kriteria Pengaruh Terendah:</span>
                        <strong class="ms-1" style="color: #e11d48;" id="label_worst_name">-</strong>
                    </div>
                </div>

                <!-- Bagian A: Bandingkan Kriteria Utama dengan yang Lain -->
                <div class="card border rounded-3 mb-3 shadow-none">
                    <div class="card-header py-2.5" style="background-color: #f0f9ff;">
                        <strong style="color: #0369a1; font-size: 0.88rem;">
                            Bagian A: Seberapa lebih penting <span id="label_best_code">Kriteria Utama</span> dibanding kriteria lainnya?
                        </strong>
                    </div>
                    <div class="card-body p-3">
                        @foreach($kriteria as $c)
                            <div class="row align-items-center py-2.5 border-bottom bwm-row-bo" data-crit-id="{{ $c->id_kriteria }}">
                                <div class="col-md-4">
                                    <strong class="text-dark d-block">{{ $c->nama_kriteria }} ({{ $c->kode }})</strong>
                                </div>
                                <div class="col-md-5">
                                    <input type="range" min="1" max="9" step="1" 
                                           class="bwm-slider slider-bo" 
                                           data-crit-id="{{ $c->id_kriteria }}" 
                                           value="{{ $preferensi->get($c->id_kriteria)->vektor_bo ?? ($c->id_kriteria == $bestId ? 1 : 3) }}">
                                    <div class="d-flex justify-content-between text-muted mt-1" style="font-size: 0.7rem;">
                                        <span>1 (Sama)</span>
                                        <span>3 (Cukup)</span>
                                        <span>5 (Lebih)</span>
                                        <span>7 (Sangat)</span>
                                        <span>9 (Mutlak)</span>
                                    </div>
                                </div>
                                <div class="col-md-3 text-md-end mt-1 mt-md-0">
                                    <span class="scale-badge-soft" id="bubble_bo_{{ $c->id_kriteria }}">Skala: 1</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Bagian B: Bandingkan Kriteria Lain dengan Kriteria Terendah -->
                <div class="card border rounded-3 mb-3 shadow-none">
                    <div class="card-header py-2.5" style="background-color: #f8fafc;">
                        <strong class="text-dark" style="font-size: 0.88rem;">
                            Bagian B: Seberapa lebih penting kriteria lainnya dibanding <span id="label_worst_code">Kriteria Terendah</span>?
                        </strong>
                    </div>
                    <div class="card-body p-3">
                        @foreach($kriteria as $c)
                            <div class="row align-items-center py-2.5 border-bottom bwm-row-ow" data-crit-id="{{ $c->id_kriteria }}">
                                <div class="col-md-4">
                                    <strong class="text-dark d-block">{{ $c->nama_kriteria }} ({{ $c->kode }})</strong>
                                </div>
                                <div class="col-md-5">
                                    <input type="range" min="1" max="9" step="1" 
                                           class="bwm-slider slider-ow" 
                                           data-crit-id="{{ $c->id_kriteria }}" 
                                           value="{{ $preferensi->get($c->id_kriteria)->vektor_ow ?? ($c->id_kriteria == $worstId ? 1 : 3) }}">
                                    <div class="d-flex justify-content-between text-muted mt-1" style="font-size: 0.7rem;">
                                        <span>1 (Sama)</span>
                                        <span>3 (Cukup)</span>
                                        <span>5 (Lebih)</span>
                                        <span>7 (Sangat)</span>
                                        <span>9 (Mutlak)</span>
                                    </div>
                                </div>
                                <div class="col-md-3 text-md-end mt-1 mt-md-0">
                                    <span class="scale-badge-soft" id="bubble_ow_{{ $c->id_kriteria }}">Skala: 1</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-light rounded-pill px-4 border" onclick="goToStep(1)">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </button>
                    <button type="button" class="btn rounded-pill px-4 py-2.5 fw-semibold text-white shadow-sm" style="background-color: #0284c7; border: none;" onclick="processCalculation()">
                        Hitung Bobot Otomatis <i class="bi bi-calculator ms-1"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- LANGKAH 3 -->
        <div class="card-custom wizard-pane d-none" id="step-pane-3">
            <div class="card-header">
                <div>
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-check-circle-fill me-2" style="color: #10b981;"></i>Langkah 3: Hasil Bobot Kriteria & Status Validasi</h6>
                    <small class="text-muted">Hasil perhitungan persentase bobot yang akan dipakai untuk merangking warga</small>
                </div>
            </div>
            <div class="card-body p-4">
                <!-- Status Box Ramah & Soft -->
                <div class="p-3 rounded-3 mb-4 text-center border" id="crCardContainer" style="background-color: #ecfdf5; border-color: #a7f3d0 !important;">
                    <h5 class="fw-bold mb-1" style="color: #065f46;" id="resCRTitle">Memeriksa...</h5>
                    <p class="text-muted mb-0" id="resCRDesc" style="font-size: 0.88rem;">-</p>
                </div>

                <!-- Hasil Bobot Ringkas -->
                <div class="row g-4 mb-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark mb-2"><i class="bi bi-bar-chart me-1" style="color: #0284c7;"></i>Diagram Bobot Kriteria</h6>
                        <div style="height: 230px;">
                            <canvas id="bwmWeightsChart"></canvas>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark mb-2"><i class="bi bi-list-ol me-1" style="color: #0284c7;"></i>Tabel Persentase Bobot</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle mb-0">
                                <thead class="table-light text-muted" style="font-size: 0.78rem;">
                                    <tr>
                                        <th>Kriteria</th>
                                        <th class="text-center" style="width: 100px;">Bobot</th>
                                        <th class="text-center" style="width: 90px;">Persentase</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 0.85rem;" id="resTableBody">
                                    <!-- Rendered dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Bagian Akademis / Skripsi (Collapsible) -->
                <div class="accordion mb-4" id="accordionSkripsi">
                    <div class="accordion-item rounded-3 border">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed py-2 px-3 bg-light text-muted fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSkripsi">
                                <i class="bi bi-mortarboard me-2" style="color: #0284c7;"></i> Detail Nilai Uji Konsistensi (Untuk Sidang Skripsi)
                            </button>
                        </h2>
                        <div id="collapseSkripsi" class="accordion-collapse collapse" data-bs-parent="#accordionSkripsi">
                            <div class="accordion-body p-3 text-muted" style="font-size: 0.82rem;">
                                <div class="row text-center g-2">
                                    <div class="col-4">
                                        <div class="p-2 border rounded bg-white">
                                            <span class="d-block fw-bold text-dark fs-6" id="resCRVal">0.0000</span>
                                            <small>Consistency Ratio (CR)</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 border rounded bg-white">
                                            <span class="d-block fw-bold text-dark fs-6" id="resXiVal">0.0000</span>
                                            <small>Deviasi Nilai (&xi;*)</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 border rounded bg-white">
                                            <span class="d-block fw-bold text-dark fs-6" id="resCIVal">0.00</span>
                                            <small>Consistency Index (CI)</small>
                                        </div>
                                    </div>
                                </div>
                                <small class="d-block text-center mt-2 text-muted">
                                    Indeks Konsistensi Berdasarkan Skala $a_{BW}$ = <strong id="resABWVal">1</strong> (Rezaei, 2015).
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between pt-2 border-top">
                    <button type="button" class="btn btn-light rounded-pill px-4 border" onclick="goToStep(2)">
                        <i class="bi bi-arrow-left me-1"></i> Sesuaikan Slider
                    </button>
                    <button type="button" class="btn rounded-pill px-4 py-2.5 fw-semibold text-white shadow-sm" style="background-color: #059669; border: none;" onclick="saveBwmResults()">
                        <i class="bi bi-check2-circle me-1"></i> Terapkan & Simpan Bobot Ini
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    const scaleMeaning = {
        1: 'Sama Penting (1)',
        2: 'Sedikit Lebih Penting (2)',
        3: 'Cukup Lebih Penting (3)',
        4: 'Lebih Tinggi (4)',
        5: 'Jauh Lebih Penting (5)',
        6: 'Kuat Lebih Penting (6)',
        7: 'Sangat Lebih Penting (7)',
        8: 'Mendekati Mutlak (8)',
        9: 'Mutlak Paling Utama (9)'
    };

    let bwmWeightsChartInstance = null;
    let lastCalculationData = null;

    $(document).ready(function() {
        $('.bwm-slider').on('input change', function() {
            const val = $(this).val();
            const critId = $(this).data('crit-id');
            const isBo = $(this).hasClass('slider-bo');
            const targetBubble = isBo ? `#bubble_bo_${critId}` : `#bubble_ow_${critId}`;
            $(targetBubble).text(scaleMeaning[val] || `Skala: ${val}`);
        });

        $('.bwm-slider').trigger('input');
    });

    function goToStep(step) {
        if (step === 2) {
            const bestId = $('#select_best').val();
            const worstId = $('#select_worst').val();

            if (bestId === worstId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilihan Masih Sama',
                    text: 'Kriteria Paling Utama dan Paling Rendah tidak boleh sama. Silakan pilih kriteria yang berbeda.'
                });
                return;
            }

            $('#label_best_name').text($('#select_best option:selected').text());
            $('#label_worst_name').text($('#select_worst option:selected').text());
            $('#label_best_code').text($('#select_best option:selected').text().split('-')[0].trim());
            $('#label_worst_code').text($('#select_worst option:selected').text().split('-')[0].trim());

            $(`.slider-bo[data-crit-id="${bestId}"]`).val(1).prop('disabled', true).trigger('input');
            $(`.slider-bo:not([data-crit-id="${bestId}"])`).prop('disabled', false);

            $(`.slider-ow[data-crit-id="${worstId}"]`).val(1).prop('disabled', true).trigger('input');
            $(`.slider-ow:not([data-crit-id="${worstId}"])`).prop('disabled', false);
        }

        $('.wizard-pane').addClass('d-none');
        $(`#step-pane-${step}`).removeClass('d-none');

        $('.wizard-step-pill').removeClass('active completed');
        for (let i = 1; i < step; i++) {
            $(`#pill-step-${i}`).addClass('completed');
        }
        $(`#pill-step-${step}`).addClass('active');

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function collectFormData() {
        const bestId = $('#select_best').val();
        const worstId = $('#select_worst').val();

        const bo = {};
        $('.slider-bo').each(function() {
            bo[$(this).data('crit-id')] = $(this).val();
        });
        bo[bestId] = 1;

        const ow = {};
        $('.slider-ow').each(function() {
            ow[$(this).data('crit-id')] = $(this).val();
        });
        ow[worstId] = 1;

        return {
            best_id: bestId,
            worst_id: worstId,
            bo: bo,
            ow: ow,
            engine: 'php',
            _token: $('meta[name="csrf-token"]').attr('content')
        };
    }

    function processCalculation() {
        const payload = collectFormData();

        Swal.fire({
            title: 'Menghitung...',
            text: 'Menganalisis keseimbangan bobot',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: "{{ route('bwm.calculate') }}",
            method: "POST",
            data: payload,
            success: function(res) {
                Swal.close();
                if (res.success) {
                    lastCalculationData = res;
                    renderStep3(res);
                    goToStep(3);
                } else {
                    Swal.fire('Gagal', res.message || 'Perhitungan gagal.', 'error');
                }
            },
            error: function(err) {
                Swal.close();
                let msg = 'Terjadi kesalahan sistem.';
                if (err.responseJSON && err.responseJSON.message) {
                    msg = err.responseJSON.message;
                }
                Swal.fire('Perhatian', msg, 'error');
            }
        });
    }

    function renderStep3(data) {
        $('#resCRVal').text(data.cr);
        $('#resXiVal').text(data.xi);
        $('#resCIVal').text(data.ci);
        $('#resABWVal').text(data.a_bw);

        const cr = parseFloat(data.cr);
        const crContainer = $('#crCardContainer');
        const crTitle = $('#resCRTitle');
        const crDesc = $('#resCRDesc');

        if (cr <= 0.15) {
            crContainer.css({'background-color': '#ecfdf5', 'border-color': '#a7f3d0'});
            crTitle.css('color', '#065f46').html('<i class="bi bi-check-circle-fill me-1" style="color: #10b981;"></i> Pembobotan Konsisten & Valid');
            crDesc.text('Perbandingan kriteria yang Anda tentukan sudah logis dan seimbang, siap diterapkan.');
        } else {
            crContainer.css({'background-color': '#fffbeb', 'border-color': '#fde68a'});
            crTitle.css('color', '#92400e').html('<i class="bi bi-exclamation-triangle-fill me-1" style="color: #f59e0b;"></i> Perbandingan Sedikit Kurang Seimbang');
            crDesc.text('Nilai perbandingan antar kriteria masih bisa digunakan, namun disarankan untuk sedikit disesuaikan kembali.');
        }

        const labels = [];
        const chartValues = [];
        let rowsHtml = '';

        for (const [cId, weight] of Object.entries(data.weights)) {
            const info = (data.kriteria_info && data.kriteria_info[cId]) ? data.kriteria_info[cId] : {};
            const kode = info.kode || `C${cId}`;
            const nama = info.nama_kriteria || '-';
            const percent = (weight * 100).toFixed(1);

            labels.push(kode);
            chartValues.push(weight);

            rowsHtml += `
                <tr>
                    <td><strong>${nama}</strong> <span class="badge border" style="background-color: #f8fafc; color: #475569;">${kode}</span></td>
                    <td class="text-center fw-semibold text-muted">${parseFloat(weight).toFixed(4)}</td>
                    <td class="text-center fw-bold" style="color: #0284c7;">${percent}%</td>
                </tr>
            `;
        }
        $('#resTableBody').html(rowsHtml);

        if (bwmWeightsChartInstance) {
            bwmWeightsChartInstance.destroy();
        }
        const ctx = document.getElementById('bwmWeightsChart').getContext('2d');
        bwmWeightsChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Bobot',
                    data: chartValues,
                    backgroundColor: '#7dd3fc', // Soft Sky Blue
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(v) { return (v * 100).toFixed(0) + '%'; }
                        }
                    }
                }
            }
        });
    }

    function saveBwmResults() {
        const payload = collectFormData();

        Swal.fire({
            title: 'Terapkan Bobot Ini?',
            text: 'Bobot baru akan digunakan untuk menghitung peringkat seluruh calon penerima bantuan.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Terapkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menyimpan...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: "{{ route('bwm.save') }}",
                    method: "POST",
                    data: payload,
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil Disimpan!',
                            text: 'Bobot kriteria telah diperbarui.'
                        }).then(() => {
                            window.location.href = "{{ route('marcos.index') }}";
                        });
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal menyimpan bobot kriteria.', 'error');
                    }
                });
            }
        });
    }
</script>
@endpush
