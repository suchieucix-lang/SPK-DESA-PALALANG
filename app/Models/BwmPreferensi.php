<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BwmPreferensi extends Model
{
    use HasFactory;

    protected $table = 'bwm_preferensi';
    protected $primaryKey = 'id_bwm';

    protected $fillable = [
        'id_kriteria',
        'vektor_bo',
        'vektor_ow',
        'is_best',
        'is_worst',
    ];

    protected function casts(): array
    {
        return [
            'vektor_bo' => 'integer',
            'vektor_ow' => 'integer',
            'is_best' => 'boolean',
            'is_worst' => 'boolean',
        ];
    }

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class, 'id_kriteria', 'id_kriteria');
    }
}

