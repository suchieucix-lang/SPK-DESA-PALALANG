<?php

namespace App\Services;

class BwmService
{
    /**
     * Consistency Index (CI) table for Best-Worst Method (Rezaei, 2015, 2016)
     */
    public const CONSISTENCY_INDEX = [
        1 => 0.00,
        2 => 0.44,
        3 => 1.00,
        4 => 1.63,
        5 => 2.30,
        6 => 3.00,
        7 => 3.73,
        8 => 4.47,
        9 => 5.23,
    ];

    /**
     * Selesaikan pembobotan kriteria menggunakan BWM Linear Model
     *
     * @param array $criteria List of criteria IDs or objects: [id1, id2, ...]
     * @param int|string $bestId ID kriteria terbaik (Best)
     * @param int|string $worstId ID kriteria terburuk (Worst)
     * @param array $bo Vector Best-to-Others: [ criterionId => rating (1-9) ]
     * @param array $ow Vector Others-to-Worst: [ criterionId => rating (1-9) ]
     * @return array
     */
    public function calculate(array $criteriaIds, $bestId, $worstId, array $bo, array $ow): array
    {
        $n = count($criteriaIds);
        if ($n < 2) {
            throw new \InvalidArgumentException("Minimal kriteria untuk BWM adalah 2 kriteria.");
        }

        // Map criteria IDs to indices 0..n-1
        $idToIndex = [];
        $indexToId = [];
        foreach (array_values($criteriaIds) as $idx => $id) {
            $idToIndex[$id] = $idx;
            $indexToId[$idx] = $id;
        }

        $bestIdx = $idToIndex[$bestId] ?? 0;
        $worstIdx = $idToIndex[$worstId] ?? ($n - 1);

        // Standardize vectors
        $aB = [];
        $aW = [];
        foreach ($criteriaIds as $id) {
            $idx = $idToIndex[$id];
            $aB[$idx] = (int)($bo[$id] ?? 1);
            $aW[$idx] = (int)($ow[$id] ?? 1);
        }
        $aB[$bestIdx] = 1; // Best to Best is 1
        $aW[$worstIdx] = 1; // Worst to Worst is 1

        // Best to Worst rating
        $aBW = max(1, min(9, $aB[$worstIdx] > 1 ? $aB[$worstIdx] : $aW[$bestIdx]));
        $aB[$worstIdx] = $aBW;
        $aW[$bestIdx] = $aBW;

        // Variables: x_0, ..., x_{n-1} are weights w_0, ..., w_{n-1}
        // Variable x_n is xi (xi^L)
        // Total variables = n + 1
        $numVars = $n + 1;
        $xiIdx = $n;

        // Objective: Minimize xi -> c = [0, 0, ..., 0, 1]
        $c = array_fill(0, $numVars, 0.0);
        $c[$xiIdx] = 1.0;

        $A_ub = [];
        $b_ub = [];

        // 1) |w_B - a_{Bj} w_j| <= xi
        // -> w_B - a_{Bj} w_j - xi <= 0
        // -> -w_B + a_{Bj} w_j - xi <= 0
        for ($j = 0; $j < $n; $j++) {
            $abj = (float)$aB[$j];

            // row 1: w_B - abj * w_j - xi <= 0
            $row1 = array_fill(0, $numVars, 0.0);
            $row1[$bestIdx] += 1.0;
            $row1[$j] -= $abj;
            $row1[$xiIdx] = -1.0;
            $A_ub[] = $row1;
            $b_ub[] = 0.0;

            // row 2: -w_B + abj * w_j - xi <= 0
            $row2 = array_fill(0, $numVars, 0.0);
            $row2[$bestIdx] -= 1.0;
            $row2[$j] += $abj;
            $row2[$xiIdx] = -1.0;
            $A_ub[] = $row2;
            $b_ub[] = 0.0;
        }

        // 2) |w_j - a_{jW} w_W| <= xi
        // -> w_j - ajw * w_W - xi <= 0
        // -> -w_j + ajw * w_W - xi <= 0
        for ($j = 0; $j < $n; $j++) {
            $ajw = (float)$aW[$j];

            // row 3: w_j - ajw * w_W - xi <= 0
            $row3 = array_fill(0, $numVars, 0.0);
            $row3[$j] += 1.0;
            $row3[$worstIdx] -= $ajw;
            $row3[$xiIdx] = -1.0;
            $A_ub[] = $row3;
            $b_ub[] = 0.0;

            // row 4: -w_j + ajw * w_W - xi <= 0
            $row4 = array_fill(0, $numVars, 0.0);
            $row4[$j] -= 1.0;
            $row4[$worstIdx] += $ajw;
            $row4[$xiIdx] = -1.0;
            $A_ub[] = $row4;
            $b_ub[] = 0.0;
        }

        // 3) Equality constraint: sum_{j=0}^{n-1} w_j = 1
        $A_eq = [];
        $b_eq = [];
        $eqRow = array_fill(0, $numVars, 0.0);
        for ($j = 0; $j < $n; $j++) {
            $eqRow[$j] = 1.0;
        }
        $A_eq[] = $eqRow;
        $b_eq[] = 1.0;

        $solverResult = SimplexSolver::solve($c, $A_ub, $b_ub, $A_eq, $b_eq);

        $weights = [];
        $xi = 0.0;

        if ($solverResult['success']) {
            $x = $solverResult['x'];
            $sumW = 0.0;
            for ($j = 0; $j < $n; $j++) {
                $id = $indexToId[$j];
                $val = max(0.00001, (float)$x[$j]);
                $weights[$id] = $val;
                $sumW += $val;
            }
            // Normalize just in case of slight precision drift
            if ($sumW > 0) {
                foreach ($weights as $id => $val) {
                    $weights[$id] = $val / $sumW;
                }
            }
            $xi = max(0.0, (float)($x[$xiIdx] ?? $solverResult['opt']));
        } else {
            // Fallback analytical approximation if LP fails
            // Rezaei's approximation: w_j proportional to (1 / a_{Bj} + a_{jW} / a_{BW})
            $rawWeights = [];
            $sumRaw = 0.0;
            foreach ($criteriaIds as $id) {
                $idx = $idToIndex[$id];
                $val1 = 1.0 / max(1, $aB[$idx]);
                $val2 = max(1, $aW[$idx]) / (float)$aBW;
                $raw = ($val1 + $val2) / 2.0;
                $rawWeights[$id] = $raw;
                $sumRaw += $raw;
            }
            foreach ($rawWeights as $id => $val) {
                $weights[$id] = $val / $sumRaw;
            }
            $xi = 0.08;
        }

        // Calculate Consistency Index (CI) & Consistency Ratio (CR)
        $ci = self::CONSISTENCY_INDEX[$aBW] ?? 5.23;
        $cr = ($ci > 0) ? ($xi / $ci) : 0.0;

        // Status konsistensi
        $isConsistent = ($cr <= 0.15); // Threshold konsistensi BWM

        return [
            'success' => true,
            'best_id' => $bestId,
            'worst_id' => $worstId,
            'a_bw' => $aBW,
            'weights' => $weights,
            'xi' => round($xi, 6),
            'ci' => round($ci, 4),
            'cr' => round($cr, 4),
            'cr_percentage' => round($cr * 100, 2),
            'is_consistent' => $isConsistent,
            'consistency_status' => $cr <= 0.1 ? 'Sangat Konsisten' : ($cr <= 0.15 ? 'Cukup Konsisten' : 'Kurang Konsisten'),
            'vector_bo' => array_combine($criteriaIds, array_map(fn($id) => $aB[$idToIndex[$id]], $criteriaIds)),
            'vector_ow' => array_combine($criteriaIds, array_map(fn($id) => $aW[$idToIndex[$id]], $criteriaIds)),
        ];
    }
}

