<?php

namespace App\Services;

/**
 * Two-Phase Simplex Linear Programming Solver
 * Solves:
 *   Minimize c^T x
 *   Subject to:
 *     A_ub * x <= b_ub
 *     A_eq * x == b_eq
 *     x >= 0
 */
class SimplexSolver
{
    protected const EPS = 1e-9;
    protected const MAX_ITER = 2000;

    /**
     * Solve Linear Program
     *
     * @param array $c Objective coefficients (minimize)
     * @param array $A_ub Matrix for inequality constraints (<=)
     * @param array $b_ub RHS for inequality constraints
     * @param array $A_eq Matrix for equality constraints (==)
     * @param array $b_eq RHS for equality constraints
     * @return array [ 'success' => bool, 'x' => array, 'opt' => float, 'message' => string ]
     */
    public static function solve(array $c, array $A_ub = [], array $b_ub = [], array $A_eq = [], array $b_eq = []): array
    {
        $num_vars = count($c);
        $rows = [];
        $rhs = [];
        $artificial_indices = [];
        $slack_indices = [];

        // Normalize constraints so all b >= 0
        $all_constraints = [];
        foreach ($A_ub as $i => $row) {
            $b = $b_ub[$i];
            if ($b < -self::EPS) {
                // multiply by -1 and change to >= (or handle)
                $neg_row = array_map(fn($v) => -$v, $row);
                $all_constraints[] = ['type' => '>=', 'row' => $neg_row, 'b' => -$b];
            } else {
                $all_constraints[] = ['type' => '<=', 'row' => $row, 'b' => $b];
            }
        }

        foreach ($A_eq as $i => $row) {
            $b = $b_eq[$i];
            if ($b < -self::EPS) {
                $neg_row = array_map(fn($v) => -$v, $row);
                $all_constraints[] = ['type' => '==', 'row' => $neg_row, 'b' => -$b];
            } else {
                $all_constraints[] = ['type' => '==', 'row' => $row, 'b' => $b];
            }
        }

        $num_constraints = count($all_constraints);
        $total_slack = 0;
        $total_artificial = 0;

        foreach ($all_constraints as $con) {
            if ($con['type'] === '<=') {
                $total_slack++;
            } elseif ($con['type'] === '>=') {
                $total_slack++; // surplus
                $total_artificial++;
            } elseif ($con['type'] === '==') {
                $total_artificial++;
            }
        }

        // Build initial tableau
        // Columns: [x_vars (num_vars), slacks/surplus (total_slack), artificials (total_artificial), RHS (1)]
        $total_cols = $num_vars + $total_slack + $total_artificial + 1;
        $tableau = array_fill(0, $num_constraints + 1, array_fill(0, $total_cols, 0.0));
        $basis = [];

        $curr_slack = 0;
        $curr_art = 0;

        for ($i = 0; $i < $num_constraints; $i++) {
            $con = $all_constraints[$i];
            for ($j = 0; $j < $num_vars; $j++) {
                $tableau[$i][$j] = (float)($con['row'][$j] ?? 0.0);
            }

            if ($con['type'] === '<=') {
                $tableau[$i][$num_vars + $curr_slack] = 1.0;
                $basis[$i] = $num_vars + $curr_slack;
                $curr_slack++;
            } elseif ($con['type'] === '>=') {
                $tableau[$i][$num_vars + $curr_slack] = -1.0; // surplus
                $curr_slack++;
                $tableau[$i][$num_vars + $total_slack + $curr_art] = 1.0; // artificial
                $artificial_indices[] = $num_vars + $total_slack + $curr_art;
                $basis[$i] = $num_vars + $total_slack + $curr_art;
                $curr_art++;
            } elseif ($con['type'] === '==') {
                $tableau[$i][$num_vars + $total_slack + $curr_art] = 1.0; // artificial
                $artificial_indices[] = $num_vars + $total_slack + $curr_art;
                $basis[$i] = $num_vars + $total_slack + $curr_art;
                $curr_art++;
            }

            $tableau[$i][$total_cols - 1] = (float)$con['b'];
        }

        // Phase 1 (if artificial variables exist)
        if ($total_artificial > 0) {
            // Objective row for Phase 1: Minimize sum of artificial variables
            for ($j = 0; $j < $total_cols; $j++) {
                $tableau[$num_constraints][$j] = 0.0;
            }
            foreach ($artificial_indices as $art_col) {
                $tableau[$num_constraints][$art_col] = 1.0;
            }

            // Adjust objective row to make it canonical with respect to basis
            for ($i = 0; $i < $num_constraints; $i++) {
                $basic_var = $basis[$i];
                if (in_array($basic_var, $artificial_indices)) {
                    for ($j = 0; $j < $total_cols; $j++) {
                        $tableau[$num_constraints][$j] -= $tableau[$i][$j];
                    }
                }
            }

            self::pivotPhase($tableau, $basis, $num_constraints, $total_cols);

            $phase1_val = $tableau[$num_constraints][$total_cols - 1];
            if (abs($phase1_val) > 1e-4) {
                return ['success' => false, 'message' => 'Problem is infeasible. Phase 1 objective: ' . $phase1_val, 'x' => [], 'opt' => 0.0];
            }
        }

        // Phase 2: Setup real objective
        for ($j = 0; $j < $total_cols; $j++) {
            $tableau[$num_constraints][$j] = 0.0;
        }
        for ($j = 0; $j < $num_vars; $j++) {
            $tableau[$num_constraints][$j] = (float)$c[$j];
        }

        // Canonicalize objective row with current basis
        for ($i = 0; $i < $num_constraints; $i++) {
            $basic_var = $basis[$i];
            if ($basic_var < $num_vars) {
                $coef = $tableau[$num_constraints][$basic_var];
                if (abs($coef) > self::EPS) {
                    for ($j = 0; $j < $total_cols; $j++) {
                        $tableau[$num_constraints][$j] -= $coef * $tableau[$i][$j];
                    }
                }
            }
        }

        // Do not allow artificial variables to re-enter
        foreach ($artificial_indices as $art_col) {
            for ($i = 0; $i <= $num_constraints; $i++) {
                $tableau[$i][$art_col] = 0.0;
            }
        }

        self::pivotPhase($tableau, $basis, $num_constraints, $total_cols);

        // Extract solution
        $x = array_fill(0, $num_vars, 0.0);
        for ($i = 0; $i < $num_constraints; $i++) {
            $var = $basis[$i];
            if ($var < $num_vars) {
                $x[$var] = max(0.0, (float)$tableau[$i][$total_cols - 1]);
            }
        }

        $opt = -$tableau[$num_constraints][$total_cols - 1];

        return [
            'success' => true,
            'x' => $x,
            'opt' => $opt,
            'message' => 'Optimal solution found',
        ];
    }

    protected static function pivotPhase(array &$tableau, array &$basis, int $num_constraints, int $total_cols): void
    {
        $iter = 0;
        while ($iter++ < self::MAX_ITER) {
            // Find entering variable (most negative in objective row)
            $pivot_col = -1;
            $min_val = -self::EPS;

            for ($j = 0; $j < $total_cols - 1; $j++) {
                if ($tableau[$num_constraints][$j] < $min_val) {
                    $min_val = $tableau[$num_constraints][$j];
                    $pivot_col = $j;
                }
            }

            if ($pivot_col === -1) {
                // Optimal found
                break;
            }

            // Find leaving variable (minimum ratio test)
            $pivot_row = -1;
            $min_ratio = INF;

            for ($i = 0; $i < $num_constraints; $i++) {
                $val = $tableau[$i][$pivot_col];
                if ($val > self::EPS) {
                    $ratio = $tableau[$i][$total_cols - 1] / $val;
                    if ($ratio >= -self::EPS && $ratio < $min_ratio) {
                        $min_ratio = $ratio;
                        $pivot_row = $i;
                    }
                }
            }

            if ($pivot_row === -1) {
                // Unbounded
                break;
            }

            // Pivot on ($pivot_row, $pivot_col)
            $pivot_val = $tableau[$pivot_row][$pivot_col];
            for ($j = 0; $j < $total_cols; $j++) {
                $tableau[$pivot_row][$j] /= $pivot_val;
            }
            $basis[$pivot_row] = $pivot_col;

            for ($i = 0; $i <= $num_constraints; $i++) {
                if ($i !== $pivot_row) {
                    $factor = $tableau[$i][$pivot_col];
                    if (abs($factor) > self::EPS) {
                        for ($j = 0; $j < $total_cols; $j++) {
                            $tableau[$i][$j] -= $factor * $tableau[$pivot_row][$j];
                        }
                    }
                }
            }
        }
    }
}

