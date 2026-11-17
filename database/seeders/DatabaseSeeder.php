<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Kriteria;
use App\Models\Warga;
use App\Models\BwmPreferensi;
use App\Models\Penilaian;
use App\Models\HasilMarcos;
use App\Models\PengaturanDesa;
use App\Services\BwmService;
use App\Services\MarcosService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin Perangkat Desa
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'nama_admin' => 'Ahmad Junaidi (Kaur Kesra Desa Palalang)',
                'email' => 'admin@desapalalang.id',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // 2. Pengaturan Desa Palalang
        PengaturanDesa::updateOrCreate(
            ['id' => 1],
            [
                'nama_desa' => 'Desa Palalang',
                'kecamatan' => 'Pakong',
                'kabupaten' => 'Pamekasan',
                'provinsi' => 'Jawa Timur',
                'alamat_kantor' => 'Jl. Balai Desa Palalang No. 01, Kec. Pakong, Kab. Pamekasan',
                'kode_pos' => '69352',
                'nama_kades' => 'H. Moh. Fadli, S.Pd.',
                'nip_kades' => '19780514 200501 1 004',
                'nama_bpd' => 'K.H. Zainal Arifin',
                'email_desa' => 'pemdes.palalang@pamekasankab.go.id',
                'telepon_desa' => '0812-3456-7890',
                'kuota_bantuan' => 8,
            ]
        );

        // 3. Kriteria Kerentanan Keluarga (DTKS & Bantuan Sosial)
        $kriteriaData = [
            [
                'kode' => 'C1',
                'nama_kriteria' => 'Penghasilan Bulanan (Rp)',
                'jenis' => 'cost', // Makin rendah penghasilan, makin prioritas
                'keterangan' => 'Total pendapatan rata-rata keluarga per bulan',
                'bobot' => 0.35,
            ],
            [
                'kode' => 'C2',
                'nama_kriteria' => 'Jumlah Tanggungan Keluarga',
                'jenis' => 'benefit', // Makin banyak tanggungan, makin prioritas
                'keterangan' => 'Jumlah anggota keluarga yang menjadi tanggungan hidup',
                'bobot' => 0.25,
            ],
            [
                'kode' => 'C3',
                'nama_kriteria' => 'Kondisi Kelayakan Rumah',
                'jenis' => 'cost', // Skala 1 (Sangat Tidak Layak) s.d 5 (Sangat Layak) -> nilai kecil = lebih prioritas
                'keterangan' => 'Skala 1 (Lantai tanah, dinding bambu) s.d 5 (Permanen/Mewah)',
                'bobot' => 0.18,
            ],
            [
                'kode' => 'C4',
                'nama_kriteria' => 'Kepemilikan Aset Produktif/Berharga',
                'jenis' => 'cost', // Skala 1 (Tidak ada) s.d 5 (Banyak aset) -> nilai kecil = lebih prioritas
                'keterangan' => 'Ternak, sepeda motor, tabungan, lahan pertanian (Skala 1-5)',
                'bobot' => 0.12,
            ],
            [
                'kode' => 'C5',
                'nama_kriteria' => 'Daya Listrik & Sanitasi',
                'jenis' => 'cost', // 1: Non-PLN/Menumpang, 2: 450VA, 3: 900VA, 4: 1300VA+ -> nilai kecil = lebih prioritas
                'keterangan' => 'Kapasitas daya listrik dan fasilitas sanitasi MCK',
                'bobot' => 0.10,
            ],
        ];

        $criteriaModels = [];
        foreach ($kriteriaData as $kd) {
            $criteriaModels[$kd['kode']] = Kriteria::updateOrCreate(['kode' => $kd['kode']], $kd);
        }

        // 4. Hitung & Simpan BWM Awal (Best: C1, Worst: C5)
        $cIds = [
            $criteriaModels['C1']->id_kriteria,
            $criteriaModels['C2']->id_kriteria,
            $criteriaModels['C3']->id_kriteria,
            $criteriaModels['C4']->id_kriteria,
            $criteriaModels['C5']->id_kriteria,
        ];
        $bestId = $criteriaModels['C1']->id_kriteria;
        $worstId = $criteriaModels['C5']->id_kriteria;

        // Vektor Best-to-Others (BO) & Others-to-Worst (OW)
        $bo = [
            $criteriaModels['C1']->id_kriteria => 1,
            $criteriaModels['C2']->id_kriteria => 2,
            $criteriaModels['C3']->id_kriteria => 3,
            $criteriaModels['C4']->id_kriteria => 5,
            $criteriaModels['C5']->id_kriteria => 7,
        ];
        $ow = [
            $criteriaModels['C1']->id_kriteria => 7,
            $criteriaModels['C2']->id_kriteria => 4,
            $criteriaModels['C3']->id_kriteria => 3,
            $criteriaModels['C4']->id_kriteria => 2,
            $criteriaModels['C5']->id_kriteria => 1,
        ];

        $bwmService = new BwmService();
        $bwmResult = $bwmService->calculate($cIds, $bestId, $worstId, $bo, $ow);

        // Update bobot kriteria dari hasil BWM
        foreach ($bwmResult['weights'] as $critId => $weight) {
            Kriteria::where('id_kriteria', $critId)->update(['bobot' => $weight]);
            
            BwmPreferensi::updateOrCreate(
                ['id_kriteria' => $critId],
                [
                    'vektor_bo' => $bo[$critId] ?? 1,
                    'vektor_ow' => $ow[$critId] ?? 1,
                    'is_best' => ($critId == $bestId),
                    'is_worst' => ($critId == $worstId),
                ]
            );
        }

        // 5. Data Alternatif / Warga Desa Palalang
        $wargaList = [
            [
                'nik' => '3528051203650001',
                'nama_kk' => 'Mat Rais',
                'dusun' => 'Dusun Palalang Barat',
                'rt_rw' => 'RT 001 / RW 001',
                'alamat' => 'Kampung Tengah RT 01 RW 01 Desa Palalang',
                'no_telp' => '087812341001',
                'nilai' => ['C1' => 450000, 'C2' => 5, 'C3' => 1, 'C4' => 1, 'C5' => 1],
            ],
            [
                'nik' => '3528052407720002',
                'nama_kk' => 'Hasyim Asyari',
                'dusun' => 'Dusun Palalang Krajan',
                'rt_rw' => 'RT 002 / RW 001',
                'alamat' => 'Krajan RT 02 RW 01 Desa Palalang',
                'no_telp' => '087812341002',
                'nilai' => ['C1' => 600000, 'C2' => 4, 'C3' => 1.5, 'C4' => 1, 'C5' => 2],
            ],
            [
                'nik' => '3528050911800003',
                'nama_kk' => 'Kholilur Rahman',
                'dusun' => 'Dusun Palalang Timur',
                'rt_rw' => 'RT 001 / RW 002',
                'alamat' => 'Bukit Harapan RT 01 RW 02 Desa Palalang',
                'no_telp' => '087812341003',
                'nilai' => ['C1' => 500000, 'C2' => 4, 'C3' => 2, 'C4' => 1, 'C5' => 1],
            ],
            [
                'nik' => '3528051508680004',
                'nama_kk' => 'Busairi',
                'dusun' => 'Dusun Palalang Barat',
                'rt_rw' => 'RT 003 / RW 001',
                'alamat' => 'Jl. Madrasah RT 03 RW 01 Desa Palalang',
                'no_telp' => '087812341004',
                'nilai' => ['C1' => 750000, 'C2' => 3, 'C3' => 2, 'C4' => 2, 'C5' => 2],
            ],
            [
                'nik' => '3528052104750005',
                'nama_kk' => 'Syaiful Bahri',
                'dusun' => 'Dusun Palalang Krajan',
                'rt_rw' => 'RT 001 / RW 001',
                'alamat' => 'Jl. Posyandu No. 4 RT 01 RW 01',
                'no_telp' => '087812341005',
                'nilai' => ['C1' => 900000, 'C2' => 3, 'C3' => 3, 'C4' => 2, 'C5' => 2],
            ],
            [
                'nik' => '3528051010700006',
                'nama_kk' => 'Nurul Huda',
                'dusun' => 'Dusun Palalang Timur',
                'rt_rw' => 'RT 002 / RW 002',
                'alamat' => 'Dusun Timur Lapangan RT 02 RW 02',
                'no_telp' => '087812341006',
                'nilai' => ['C1' => 400000, 'C2' => 6, 'C3' => 1, 'C4' => 1, 'C5' => 1],
            ],
            [
                'nik' => '3528050406830007',
                'nama_kk' => 'Moh. Subhan',
                'dusun' => 'Dusun Palalang Krajan',
                'rt_rw' => 'RT 003 / RW 001',
                'alamat' => 'Jl. Pesantren No. 10 RT 03 RW 01',
                'no_telp' => '087812341007',
                'nilai' => ['C1' => 1100000, 'C2' => 2, 'C3' => 3, 'C4' => 3, 'C5' => 3],
            ],
            [
                'nik' => '3528051809620008',
                'nama_kk' => 'Achmad Sukardi',
                'dusun' => 'Dusun Palalang Barat',
                'rt_rw' => 'RT 002 / RW 001',
                'alamat' => 'Barat Masjid Al-Hidayah RT 02 RW 01',
                'no_telp' => '087812341008',
                'nilai' => ['C1' => 550000, 'C2' => 4, 'C3' => 2, 'C4' => 1.5, 'C5' => 2],
            ],
            [
                'nik' => '3528050512860009',
                'nama_kk' => 'Zainuddin',
                'dusun' => 'Dusun Palalang Timur',
                'rt_rw' => 'RT 003 / RW 002',
                'alamat' => 'Blok Sumber Air RT 03 RW 02',
                'no_telp' => '087812341009',
                'nilai' => ['C1' => 1350000, 'C2' => 2, 'C3' => 4, 'C4' => 3, 'C5' => 3],
            ],
            [
                'nik' => '3528052901770010',
                'nama_kk' => 'Jamilah (Lansia Mandiri)',
                'dusun' => 'Dusun Palalang Krajan',
                'rt_rw' => 'RT 002 / RW 001',
                'alamat' => 'Gang Melati RT 02 RW 01',
                'no_telp' => '087812341010',
                'nilai' => ['C1' => 300000, 'C2' => 1, 'C3' => 1, 'C4' => 1, 'C5' => 1],
            ],
            [
                'nik' => '3528051703810011',
                'nama_kk' => 'Farid Wajdi',
                'dusun' => 'Dusun Palalang Barat',
                'rt_rw' => 'RT 001 / RW 001',
                'alamat' => 'Jl. Pertanian RT 01 RW 01',
                'no_telp' => '087812341011',
                'nilai' => ['C1' => 850000, 'C2' => 3, 'C3' => 2.5, 'C4' => 2, 'C5' => 2],
            ],
            [
                'nik' => '3528051111790012',
                'nama_kk' => 'Siti Aminah',
                'dusun' => 'Dusun Palalang Timur',
                'rt_rw' => 'RT 001 / RW 002',
                'alamat' => 'Timur Balai Pertemuan RT 01 RW 02',
                'no_telp' => '087812341012',
                'nilai' => ['C1' => 520000, 'C2' => 5, 'C3' => 1.5, 'C4' => 1, 'C5' => 1],
            ],
        ];

        $wargaModels = [];
        foreach ($wargaList as $w) {
            $nilaiData = $w['nilai'];
            unset($w['nilai']);
            $wModel = Warga::updateOrCreate(['nik' => $w['nik']], $w);
            $wargaModels[] = $wModel;

            // Simpan penilaian tiap kriteria
            foreach ($nilaiData as $kKode => $nVal) {
                if (isset($criteriaModels[$kKode])) {
                    Penilaian::updateOrCreate(
                        [
                            'id_warga' => $wModel->id_warga,
                            'id_kriteria' => $criteriaModels[$kKode]->id_kriteria,
                        ],
                        [
                            'nilai_riil' => $nVal,
                        ]
                    );
                }
            }
        }

        // 6. Jalankan Kalkulasi MARCOS dan Simpan Hasil
        $freshCriteria = Kriteria::all()->map(function ($c) {
            return [
                'id' => $c->id_kriteria,
                'kode' => $c->kode,
                'nama' => $c->nama_kriteria,
                'jenis' => $c->jenis,
                'bobot' => (float)$c->bobot,
            ];
        })->toArray();

        $freshAlternatives = Warga::all()->map(function ($w) {
            return [
                'id' => $w->id_warga,
                'nik' => $w->nik,
                'nama' => $w->nama_kk,
                'dusun' => $w->dusun,
                'rt_rw' => $w->rt_rw,
            ];
        })->toArray();

        $freshMatrix = [];
        foreach (Penilaian::all() as $p) {
            $freshMatrix[$p->id_warga][$p->id_kriteria] = (float)$p->nilai_riil;
        }

        $marcosService = new MarcosService();
        $marcosResult = $marcosService->calculate($freshAlternatives, $freshCriteria, $freshMatrix);

        if ($marcosResult['success']) {
            HasilMarcos::truncate();
            foreach ($marcosResult['rankings'] as $item) {
                HasilMarcos::create([
                    'id_warga' => $item['id_warga'],
                    'skor_utilitas' => $item['skor_utilitas'],
                    'peringkat' => $item['peringkat'],
                    'k_plus' => $item['k_plus'],
                    'k_minus' => $item['k_minus'],
                ]);
            }
        }
    }
}
