<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabel Kriteria
        Schema::create('kriteria', function (Blueprint $table) {
            $table->id('id_kriteria');
            $table->string('kode', 10)->unique();
            $table->string('nama_kriteria');
            $table->enum('jenis', ['benefit', 'cost']);
            $table->double('bobot')->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 2. Tabel Warga (Alternatif)
        Schema::create('warga', function (Blueprint $table) {
            $table->id('id_warga');
            $table->string('nik', 16)->unique();
            $table->string('nama_kk');
            $table->string('dusun');
            $table->string('rt_rw', 20);
            $table->text('alamat')->nullable();
            $table->string('no_telp', 20)->nullable();
            $table->timestamps();
        });

        // 3. Tabel BWM Preferensi
        Schema::create('bwm_preferensi', function (Blueprint $table) {
            $table->id('id_bwm');
            $table->unsignedBigInteger('id_kriteria');
            $table->integer('vektor_bo')->default(1); // Perbandingan Best to Others
            $table->integer('vektor_ow')->default(1); // Perbandingan Others to Worst
            $table->boolean('is_best')->default(false);
            $table->boolean('is_worst')->default(false);
            $table->timestamps();

            $table->foreign('id_kriteria')
                  ->references('id_kriteria')
                  ->on('kriteria')
                  ->onDelete('cascade');
        });

        // 4. Tabel Penilaian (Matriks Keputusan Riil Warga)
        Schema::create('penilaian', function (Blueprint $table) {
            $table->id('id_penilaian');
            $table->unsignedBigInteger('id_warga');
            $table->unsignedBigInteger('id_kriteria');
            $table->double('nilai_riil');
            $table->timestamps();

            $table->foreign('id_warga')
                  ->references('id_warga')
                  ->on('warga')
                  ->onDelete('cascade');

            $table->foreign('id_kriteria')
                  ->references('id_kriteria')
                  ->on('kriteria')
                  ->onDelete('cascade');

            $table->unique(['id_warga', 'id_kriteria']);
        });

        // 5. Tabel Hasil MARCOS
        Schema::create('hasil_marcos', function (Blueprint $table) {
            $table->id('id_hasil');
            $table->unsignedBigInteger('id_warga');
            $table->double('skor_utilitas'); // Nilai f(K)
            $table->integer('peringkat');
            $table->double('k_plus')->nullable();
            $table->double('k_minus')->nullable();
            $table->timestamps();

            $table->foreign('id_warga')
                  ->references('id_warga')
                  ->on('warga')
                  ->onDelete('cascade');
        });

        // 6. Tabel Pengaturan Profil Desa (untuk Kop Surat dan Laporan Resmi)
        Schema::create('pengaturan_desa', function (Blueprint $table) {
            $table->id();
            $table->string('nama_desa')->default('Desa Palalang');
            $table->string('kecamatan')->default('Pakong');
            $table->string('kabupaten')->default('Pamekasan');
            $table->string('provinsi')->default('Jawa Timur');
            $table->string('alamat_kantor')->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('nama_kades')->nullable();
            $table->string('nip_kades', 30)->nullable();
            $table->string('nama_bpd')->nullable();
            $table->string('email_desa')->nullable();
            $table->string('telepon_desa')->nullable();
            $table->integer('kuota_bantuan')->default(10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan_desa');
        Schema::dropIfExists('hasil_marcos');
        Schema::dropIfExists('penilaian');
        Schema::dropIfExists('bwm_preferensi');
        Schema::dropIfExists('warga');
        Schema::dropIfExists('kriteria');
    }
};

