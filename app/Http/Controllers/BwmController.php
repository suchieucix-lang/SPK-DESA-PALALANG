<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\BwmPreferensi;
use App\Services\BwmService;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class BwmController extends Controller
{
    protected BwmService $bwmService;

    public function __construct(BwmService $bwmService)
    {
        $this->bwmService = $bwmService;
    }

    public function index()
    {
        $kriteria = Kriteria::orderBy('kode', 'asc')->get();
        if ($kriteria->count() < 2) {
            return redirect()->route('kriteria.index')
                ->with('error', 'Kriteria minimal berjumlah 2 untuk melakukan pembobotan BWM.');
        }

        // Ambil preferensi tersimpan
        $preferensi = BwmPreferensi::with('kriteria')->get()->keyBy('id_kriteria');
        $bestId = BwmPreferensi::where('is_best', true)->value('id_kriteria') ?? $kriteria->first()->id_kriteria;
        $worstId = BwmPreferensi::where('is_worst', true)->value('id_kriteria') ?? $kriteria->last()->id_kriteria;

        // Skala Saaty / BWM text representation
        $scaleMap = [
            1 => '1 - Sama Penting (Equal importance)',
            2 => '2 - Sedikit Lebih Penting (Antara 1 & 3)',
            3 => '3 - Cukup Penting (Moderate importance)',
            4 => '4 - Sedikit Lebih Tinggi (Antara 3 & 5)',
            5 => '5 - Lebih Penting (Strong importance)',
            6 => '6 - Antara Kuat & Sangat Kuat (Antara 5 & 7)',
            7 => '7 - Sangat Penting (Very strong importance)',
            8 => '8 - Mendekati Mutlak (Antara 7 & 9)',
            9 => '9 - Mutlak Lebih Penting (Extreme importance)',
        ];

        // Lakukan kalkulasi awal jika data sudah ada
        $initialCalc = null;
        try {
            $cIds = $kriteria->pluck('id_kriteria')->toArray();
            $bo = [];
            $ow = [];
            foreach ($kriteria as $c) {
                $pref = $preferensi->get($c->id_kriteria);
                $bo[$c->id_kriteria] = $pref ? $pref->vektor_bo : ($c->id_kriteria == $bestId ? 1 : 3);
                $ow[$c->id_kriteria] = $pref ? $pref->vektor_ow : ($c->id_kriteria == $worstId ? 1 : 3);
            }
            $initialCalc = $this->bwmService->calculate($cIds, $bestId, $worstId, $bo, $ow);
        } catch (\Exception $e) {
            $initialCalc = null;
        }

        return view('bwm.index', compact(
            'kriteria',
            'preferensi',
            'bestId',
            'worstId',
            'scaleMap',
            'initialCalc'
        ));
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'best_id' => 'required|exists:kriteria,id_kriteria',
            'worst_id' => 'required|exists:kriteria,id_kriteria|different:best_id',
            'bo' => 'required|array',
            'ow' => 'required|array',
            'engine' => 'nullable|in:php,python',
        ]);

        $bestId = (int)$request->best_id;
        $worstId = (int)$request->worst_id;
        $bo = $request->bo;
        $ow = $request->ow;
        $engine = $request->input('engine', 'php');

        $kriteria = Kriteria::orderBy('kode', 'asc')->get();
        $cIds = $kriteria->pluck('id_kriteria')->toArray();

        // Hybrid Python Option
        if ($engine === 'python') {
            try {
                $pyScript = app_path('Services/Python/bwm_marcos_engine.py');
                $inputJson = json_encode([
                    'action' => 'bwm',
                    'payload' => [
                        'criteria' => $cIds,
                        'best_id' => $bestId,
                        'worst_id' => $worstId,
                        'bo' => $bo,
                        'ow' => $ow,
                    ]
                ]);

                $process = new Process(['python', $pyScript]);
                $process->setInput($inputJson);
                $process->run();

                if ($process->isSuccessful()) {
                    $pyOutput = json_decode($process->getOutput(), true);
                    if ($pyOutput && isset($pyOutput['success']) && $pyOutput['success']) {
                        return response()->json(array_merge($pyOutput, [
                            'engine_used' => 'Python Hybrid Engine',
                            'best_id' => $bestId,
                            'worst_id' => $worstId,
                            'kriteria_info' => $kriteria->keyBy('id_kriteria'),
                        ]));
                    }
                }
            } catch (\Exception $e) {
                // fallback to PHP
            }
        }

        // Native High-Precision PHP Simplex BWM
        try {
            $result = $this->bwmService->calculate($cIds, $bestId, $worstId, $bo, $ow);
            $result['engine_used'] = 'PHP Native High-Precision LP Simplex';
            $result['kriteria_info'] = $kriteria->keyBy('id_kriteria');
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghitung BWM: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function save(Request $request)
    {
        $request->validate([
            'best_id' => 'required|exists:kriteria,id_kriteria',
            'worst_id' => 'required|exists:kriteria,id_kriteria|different:best_id',
            'bo' => 'required|array',
            'ow' => 'required|array',
        ]);

        $bestId = (int)$request->best_id;
        $worstId = (int)$request->worst_id;
        $bo = $request->bo;
        $ow = $request->ow;

        $kriteria = Kriteria::orderBy('kode', 'asc')->get();
        $cIds = $kriteria->pluck('id_kriteria')->toArray();

        $calc = $this->bwmService->calculate($cIds, $bestId, $worstId, $bo, $ow);

        // Update bobot pada tabel kriteria dan simpan preferensi
        foreach ($calc['weights'] as $cId => $weight) {
            Kriteria::where('id_kriteria', $cId)->update(['bobot' => $weight]);

            BwmPreferensi::updateOrCreate(
                ['id_kriteria' => $cId],
                [
                    'vektor_bo' => (int)($bo[$cId] ?? 1),
                    'vektor_ow' => (int)($ow[$cId] ?? 1),
                    'is_best' => ($cId == $bestId),
                    'is_worst' => ($cId == $worstId),
                ]
            );
        }

        $msg = "Pembobotan BWM berhasil disimpan! Consistency Ratio (CR) = " . $calc['cr'] . " (" . $calc['consistency_status'] . ").";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'data' => $calc,
            ]);
        }

        return redirect()->route('bwm.index')->with('success', $msg);
    }
}

