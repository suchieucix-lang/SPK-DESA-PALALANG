<?php

namespace App\Services;

class MarcosService
{
    /**
     * Hitung metode MARCOS lengkap beserta seluruh matriks analisis
     *
     * @param array $alternatives List of alternatives: [ ['id' => 1, 'nama' => '...'], ... ]
     * @param array $criteria List of criteria: [ ['id' => 1, 'kode' => 'C1', 'nama' => '...', 'jenis' => 'benefit|cost', 'bobot' => 0.25], ... ]
     * @param array $matrix Nilai riil: [ alt_id => [ crit_id => float_val, ... ], ... ]
     * @return array
     */
    public function calculate(array $alternatives, array $criteria, array $matrix): array
    {
        if (empty($alternatives) || empty($criteria)) {
            return [
                'success' => false,
                'message' => 'Data alternatif atau kriteria belum lengkap.',
            ];
        }

        // 1. Ekstrak kriteria & bobot
        $critMap = [];
        $weights = [];
        $critTypes = [];
        $totalWeight = 0.0;

        foreach ($criteria as $c) {
            $cId = $c['id'];
            $critMap[$cId] = $c;
            $critTypes[$cId] = strtolower($c['jenis'] ?? 'benefit');
            $w = (float)($c['bobot'] ?? 0);
            $weights[$cId] = $w;
            $totalWeight += $w;
        }

        // Normalisasi bobot jika total bobot > 0 dan belum 1
        if ($totalWeight > 0 && abs($totalWeight - 1.0) > 0.01) {
            foreach ($weights as $cId => $w) {
                $weights[$cId] = $w / $totalWeight;
            }
        } elseif ($totalWeight == 0) {
            $equalWeight = 1.0 / count($criteria);
            foreach ($criteria as $c) {
                $weights[$c['id']] = $equalWeight;
            }
        }

        // 2. Siapkan Matriks Keputusan X
        $X = [];
        $altMap = [];
        foreach ($alternatives as $alt) {
            $aId = $alt['id'];
            $altMap[$aId] = $alt;
            $X[$aId] = [];
            foreach ($criteria as $c) {
                $cId = $c['id'];
                // Default ke 0 jika belum dinilai
                $val = isset($matrix[$aId][$cId]) ? (float)$matrix[$aId][$cId] : 0.0;
                $X[$aId][$cId] = $val;
            }
        }

        // 3. Tentukan Solusi Ideal (AI) dan Anti-Ideal (AAI)
        $AI = [];
        $AAI = [];

        foreach ($criteria as $c) {
            $cId = $c['id'];
            $type = $critTypes[$cId];

            $colValues = [];
            foreach ($alternatives as $alt) {
                $colValues[] = $X[$alt['id']][$cId];
            }

            $maxVal = !empty($colValues) ? max($colValues) : 0.0;
            $minVal = !empty($colValues) ? min($colValues) : 0.0;

            if ($type === 'benefit') {
                $AI[$cId] = $maxVal;
                $AAI[$cId] = $minVal;
            } else { // cost
                $AI[$cId] = $minVal;
                $AAI[$cId] = $maxVal;
            }
        }

        // 4. Matriks Ternormalisasi N (Termasuk baris AAI dan AI)
        $N = [];
        $extendedRows = ['AAI' => $AAI];
        foreach ($alternatives as $alt) {
            $extendedRows[$alt['id']] = $X[$alt['id']];
        }
        $extendedRows['AI'] = $AI;

        foreach ($extendedRows as $rowKey => $rowVals) {
            $N[$rowKey] = [];
            foreach ($criteria as $c) {
                $cId = $c['id'];
                $type = $critTypes[$cId];
                $ai = (float)$AI[$cId];
                $xVal = (float)$rowVals[$cId];

                if ($type === 'benefit') {
                    // n_ij = x_ij / x_ai
                    $N[$rowKey][$cId] = ($ai != 0.0) ? ($xVal / $ai) : 0.0;
                } else {
                    // cost: n_ij = x_ai / x_ij
                    $N[$rowKey][$cId] = ($xVal != 0.0) ? ($ai / $xVal) : 0.0;
                }
            }
        }

        // 5. Matriks Terbobot V: v_ij = n_ij * w_j
        $V = [];
        $S = []; // Total baris S_i

        foreach ($extendedRows as $rowKey => $rowVals) {
            $V[$rowKey] = [];
            $sumV = 0.0;
            foreach ($criteria as $c) {
                $cId = $c['id'];
                $vVal = $N[$rowKey][$cId] * $weights[$cId];
                $V[$rowKey][$cId] = $vVal;
                $sumV += $vVal;
            }
            $S[$rowKey] = $sumV;
        }

        $S_aai = $S['AAI'];
        $S_ai = $S['AI'];

        // Cegah pembagian dengan nol
        $S_aai = ($S_aai <= 0.0) ? 0.0001 : $S_aai;
        $S_ai = ($S_ai <= 0.0) ? 0.0001 : $S_ai;

        // 6. Hitung Derajat Utilitas K- dan K+ serta Fungsi Utilitas f(K)
        $K_minus = [];
        $K_plus = [];
        $f_K_minus = [];
        $f_K_plus = [];
        $f_K = [];
        $rankings = [];

        foreach ($alternatives as $alt) {
            $aId = $alt['id'];
            $s_i = $S[$aId];

            $kMin = $s_i / $S_aai;
            $kPlu = $s_i / $S_ai;

            $K_minus[$aId] = $kMin;
            $K_plus[$aId] = $kPlu;

            // f(K-) = K+ / (K+ + K-)
            // f(K+) = K- / (K+ + K-)
            $sumK = $kPlu + $kMin;
            $fKMin = ($sumK > 0) ? ($kPlu / $sumK) : 0.0;
            $fKPlu = ($sumK > 0) ? ($kMin / $sumK) : 0.0;

            $f_K_minus[$aId] = $fKMin;
            $f_K_plus[$aId] = $fKPlu;

            // Compromise utility function f(K):
            // f(K) = (K+ + K-) / (1 + ((1 - f(K+))/f(K+)) + ((1 - f(K-))/f(K-)))
            // Note: ((1 - f(K+))/f(K+)) = (1 - (K- / (K+ + K-))) / (K- / (K+ + K-)) = K+ / K-
            // Similarly: ((1 - f(K-))/f(K-)) = K- / K+
            // denominator = 1 + (K+/K-) + (K-/K+)
            $term1 = ($fKPlu > 0) ? ((1.0 - $fKPlu) / $fKPlu) : 0.0;
            $term2 = ($fKMin > 0) ? ((1.0 - $fKMin) / $fKMin) : 0.0;
            $denom = 1.0 + $term1 + $term2;

            $fKVal = ($denom > 0) ? ($sumK / $denom) : 0.0;
            $f_K[$aId] = $fKVal;

            $rankings[] = [
                'id_warga' => $aId,
                'nik' => $alt['nik'] ?? '',
                'nama' => $alt['nama'] ?? ($alt['nama_kk'] ?? ''),
                'dusun' => $alt['dusun'] ?? '',
                'rt_rw' => $alt['rt_rw'] ?? '',
                's_i' => round($s_i, 5),
                'k_minus' => round($kMin, 5),
                'k_plus' => round($kPlu, 5),
                'f_k_minus' => round($fKMin, 5),
                'f_k_plus' => round($fKPlu, 5),
                'skor_utilitas' => round($fKVal, 5),
            ];
        }

        // Urutkan ranking berdasarkan skor_utilitas (f(K)) DESCENDING
        usort($rankings, function ($a, $b) {
            if ($b['skor_utilitas'] == $a['skor_utilitas']) {
                return 0;
            }
            return ($b['skor_utilitas'] > $a['skor_utilitas']) ? 1 : -1;
        });

        // Tambahkan atribut peringkat 1..m
        foreach ($rankings as $idx => &$r) {
            $r['peringkat'] = $idx + 1;
        }
        unset($r);

        return [
            'success' => true,
            'criteria' => $criteria,
            'alternatives' => $alternatives,
            'weights' => $weights,
            'raw_matrix_X' => $X,
            'AI' => $AI,
            'AAI' => $AAI,
            'extended_matrix' => $extendedRows,
            'normalized_matrix_N' => $N,
            'weighted_matrix_V' => $V,
            'S' => array_map(fn($v) => round($v, 5), $S),
            'S_ai' => round($S_ai, 5),
            'S_aai' => round($S_aai, 5),
            'K_minus' => array_map(fn($v) => round($v, 5), $K_minus),
            'K_plus' => array_map(fn($v) => round($v, 5), $K_plus),
            'f_K_minus' => array_map(fn($v) => round($v, 5), $f_K_minus),
            'f_K_plus' => array_map(fn($v) => round($v, 5), $f_K_plus),
            'f_K' => array_map(fn($v) => round($v, 5), $f_K),
            'rankings' => $rankings,
        ];
    }
}

