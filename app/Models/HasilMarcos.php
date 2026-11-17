<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilMarcos extends Model
{
    use HasFactory;

    protected $table = 'hasil_marcos';
    protected $primaryKey = 'id_hasil';

    protected $fillable = [
        'id_warga',
        'skor_utilitas',
        'peringkat',
        'k_plus',
        'k_minus',
    ];

    protected function casts(): array
    {
        return [
            'skor_utilitas' => 'float',
            'peringkat' => 'integer',
            'k_plus' => 'float',
            'k_minus' => 'float',
        ];
    }

    public function warga()
    {
        return $this->belongsTo(Warga::class, 'id_warga', 'id_warga');
    }
}

