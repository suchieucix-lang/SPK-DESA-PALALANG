<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    use HasFactory;

    protected $table = 'kriteria';
    protected $primaryKey = 'id_kriteria';

    protected $fillable = [
        'kode',
        'nama_kriteria',
        'jenis',
        'bobot',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'bobot' => 'float',
        ];
    }

    public function preferensi()
    {
        return $this->hasOne(BwmPreferensi::class, 'id_kriteria', 'id_kriteria');
    }

    public function penilaian()
    {
        return $this->hasMany(Penilaian::class, 'id_kriteria', 'id_kriteria');
    }
}

