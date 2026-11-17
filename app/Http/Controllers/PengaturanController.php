<?php

namespace App\Http\Controllers;

use App\Models\PengaturanDesa;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function index()
    {
        $setting = PengaturanDesa::getSetting();
        return view('pengaturan.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_desa' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kabupaten' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'alamat_kantor' => 'nullable|string',
            'kode_pos' => 'nullable|string|max:10',
            'nama_kades' => 'required|string|max:255',
            'nip_kades' => 'nullable|string|max:50',
            'nama_bpd' => 'nullable|string|max:255',
            'email_desa' => 'nullable|email|max:255',
            'telepon_desa' => 'nullable|string|max:50',
            'kuota_bantuan' => 'required|integer|min:1',
        ]);

        $setting = PengaturanDesa::getSetting();
        $setting->update($request->all());

        return redirect()->route('pengaturan.index')
            ->with('success', 'Konfigurasi profil desa dan kop surat berhasil diperbarui.');
    }
}

