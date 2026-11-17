<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanDesa extends Model
{
    use HasFactory;

    protected $table = 'pengaturan_desa';

    protected $fillable = [
        'nama_desa',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'alamat_kantor',
        'kode_pos',
        'nama_kades',
        'nip_kades',
        'nama_bpd',
        'email_desa',
        'telepon_desa',
        'kuota_bantuan',
    ];

    public static function getSetting()
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'nama_desa' => 'Desa Palalang',
                'kecamatan' => 'Pakong',
                'kabupaten' => 'Pamekasan',
                'provinsi' => 'Jawa Timur',
                'alamat_kantor' => 'Jl. Raya Palalang No. 12, Pakong, Pamekasan',
                'kode_pos' => '69352',
                'nama_kades' => 'H. Achmad Syafi\'i, S.Sos.',
                'nip_kades' => '19750812 200212 1 003',
                'nama_bpd' => 'Drs. H. Mohammad Zainal',
                'email_desa' => 'pemdes.palalang@pamekasankab.go.id',
                'telepon_desa' => '(0324) 381234',
                'kuota_bantuan' => 10,
            ]
        );
    }
}

