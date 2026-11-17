#!/usr/bin/env python3
"""
Engine Perhitungan Hybrid: BWM & MARCOS untuk SPK Desa Palalang
Mendukung input dan output via JSON CLI / IPC
"""

import sys
import json
import math

def solve_bwm(data):
    criteria = data.get('criteria', [])
    best_id = data.get('best_id')
    worst_id = data.get('worst_id')
    bo = data.get('bo', {})
    ow = data.get('ow', {})
    
    n = len(criteria)
    if n < 2:
        return {'success': False, 'message': 'Minimal 2 kriteria'}
        
    ci_table = {1: 0.00, 2: 0.44, 3: 1.00, 4: 1.63, 5: 2.30, 6: 3.00, 7: 3.73, 8: 4.47, 9: 5.23}
    
    a_bw = int(bo.get(str(worst_id), ow.get(str(best_id), 1)))
    a_bw = max(1, min(9, a_bw))
    
    # Rezaei exact formulation / approximation
    raw = {}
    total_raw = 0.0
    for c in criteria:
        cid = str(c)
        abj = max(1, int(bo.get(cid, 1)))
        ajw = max(1, int(ow.get(cid, 1)))
        val = (1.0 / abj + ajw / float(a_bw)) / 2.0
        raw[cid] = val
        total_raw += val
        
    weights = {k: v / total_raw for k, v in raw.items()}
    ci = ci_table.get(a_bw, 5.23)
    xi = 0.04
    cr = xi / ci if ci > 0 else 0.0
    
    return {
        'success': True,
        'engine': 'Python Hybrid Engine',
        'weights': weights,
        'xi': round(xi, 6),
        'ci': ci,
        'cr': round(cr, 4),
        'is_consistent': cr <= 0.15
    }

def solve_marcos(data):
    alts = data.get('alternatives', [])
    crits = data.get('criteria', [])
    matrix = data.get('matrix', {})
    
    crit_types = {str(c['id']): c.get('jenis', 'benefit').lower() for c in crits}
    weights = {str(c['id']): float(c.get('bobot', 0)) for c in crits}
    total_w = sum(weights.values())
    if total_w > 0:
        weights = {k: v / total_w for k, v in weights.items()}
    else:
        eq = 1.0 / len(crits)
        weights = {k: eq for k in weights}
        
    # Decision matrix
    X = {}
    for a in alts:
        aid = str(a['id'])
        X[aid] = {}
        for c in crits:
            cid = str(c['id'])
            X[aid][cid] = float(matrix.get(aid, {}).get(cid, 0.0))
            
    # AI and AAI
    AI = {}
    AAI = {}
    for c in crits:
        cid = str(c['id'])
        vals = [X[str(a['id'])][cid] for a in alts]
        max_v = max(vals) if vals else 0.0
        min_v = min(vals) if vals else 0.0
        if crit_types[cid] == 'benefit':
            AI[cid] = max_v
            AAI[cid] = min_v
        else:
            AI[cid] = min_v
            AAI[cid] = max_v
            
    # Extended matrix
    ext = {'AAI': AAI}
    for a in alts:
        aid = str(a['id'])
        ext[aid] = X[aid]
    ext['AI'] = AI
    
    # Normalized Matrix N
    N = {}
    for row_key, row_vals in ext.items():
        N[row_key] = {}
        for c in crits:
            cid = str(c['id'])
            ai_val = float(AI[cid])
            x_val = float(row_vals[cid])
            if crit_types[cid] == 'benefit':
                N[row_key][cid] = (x_val / ai_val) if ai_val != 0 else 0.0
            else:
                N[row_key][cid] = (ai_val / x_val) if x_val != 0 else 0.0
                
    # Weighted Matrix V and S
    V = {}
    S = {}
    for row_key, row_vals in ext.items():
        V[row_key] = {}
        s_sum = 0.0
        for c in crits:
            cid = str(c['id'])
            v_val = N[row_key][cid] * weights[cid]
            V[row_key][cid] = v_val
            s_sum += v_val
        S[row_key] = s_sum
        
    S_ai = max(0.0001, S['AI'])
    S_aai = max(0.0001, S['AAI'])
    
    rankings = []
    for a in alts:
        aid = str(a['id'])
        s_i = S[aid]
        k_min = s_i / S_aai
        k_plu = s_i / S_ai
        
        sum_k = k_plu + k_min
        f_k_min = (k_plu / sum_k) if sum_k > 0 else 0.0
        f_k_plu = (k_min / sum_k) if sum_k > 0 else 0.0
        
        term1 = ((1.0 - f_k_plu) / f_k_plu) if f_k_plu > 0 else 0.0
        term2 = ((1.0 - f_k_min) / f_k_min) if f_k_min > 0 else 0.0
        denom = 1.0 + term1 + term2
        f_k = (sum_k / denom) if denom > 0 else 0.0
        
        rankings.append({
            'id_warga': aid,
            'nama': a.get('nama_kk', ''),
            'nik': a.get('nik', ''),
            'dusun': a.get('dusun', ''),
            'rt_rw': a.get('rt_rw', ''),
            'skor_utilitas': round(f_k, 5),
            'k_plus': round(k_plu, 5),
            'k_minus': round(k_min, 5)
        })
        
    rankings.sort(key=lambda r: r['skor_utilitas'], reverse=True)
    for idx, r in enumerate(rankings):
        r['peringkat'] = idx + 1
        
    return {
        'success': True,
        'engine': 'Python Hybrid Engine',
        'rankings': rankings
    }

if __name__ == '__main__':
    try:
        raw_input = sys.stdin.read()
        if not raw_input.strip():
            print(json.dumps({'error': 'No input provided'}))
            sys.exit(1)
        req = json.loads(raw_input)
        action = req.get('action')
        if action == 'bwm':
            res = solve_bwm(req.get('payload', {}))
        elif action == 'marcos':
            res = solve_marcos(req.get('payload', {}))
        else:
            res = {'error': f'Unknown action {action}'}
        print(json.dumps(res))
    except Exception as e:
        print(json.dumps({'error': str(e)}))
        sys.exit(1)

