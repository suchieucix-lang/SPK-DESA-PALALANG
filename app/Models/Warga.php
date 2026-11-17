<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warga extends Model
{
    use HasFactory;

    protected $table = 'warga';
    protected $primaryKey = 'id_warga';

    protected $fillable = [
        'nik',
        'nama_kk',
        'dusun',
        'rt_rw',
        'alamat',
        'no_telp',
    ];

    public function penilaian()
    {
        return $this->hasMany(Penilaian::class, 'id_warga', 'id_warga');
    }

    public function hasilMarcos()
    {
        return $this->hasOne(HasilMarcos::class, 'id_warga', 'id_warga');
    }

    /**
     * Mendapatkan nilai kriteria tertentu untuk warga ini
     */
    public function getNilaiKriteria($id_kriteria)
    {
        $item = $this->penilaian->firstWhere('id_kriteria', $id_kriteria);
        return $item ? $item->nilai_riil : null;
    }
}

