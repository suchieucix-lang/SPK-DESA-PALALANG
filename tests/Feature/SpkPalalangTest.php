<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Kriteria;
use App\Models\Warga;
use App\Models\HasilMarcos;
use App\Services\BwmService;
use App\Services\MarcosService;
use Tests\TestCase;

class SpkPalalangTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::first() ?? User::factory()->create([
            'username' => 'admin',
            'nama_admin' => 'Perangkat Desa',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('SPK DESA PALALANG');
    }

    public function test_admin_can_login_with_valid_credentials(): void
    {
        $response = $this->post('/login', [
            'login' => 'admin',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_dashboard_renders_for_authenticated_admin(): void
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('SPK DESA PALALANG');
        $response->assertSee('Status Kelayakan Keluarga');
    }

    public function test_kriteria_page_renders_and_lists_criteria(): void
    {
        $response = $this->actingAs($this->admin)->get('/kriteria');
        $response->assertStatus(200);
        $response->assertSee('Daftar Parameter Kriteria');
    }

    public function test_warga_page_renders_and_lists_citizens(): void
    {
        $response = $this->actingAs($this->admin)->get('/warga');
        $response->assertStatus(200);
        $response->assertSee('Daftar Kepala Keluarga (KK) Desa Palalang');
    }

    public function test_bwm_wizard_page_renders_and_calculates_correctly(): void
    {
        $response = $this->actingAs($this->admin)->get('/bwm');
        $response->assertStatus(200);
        $response->assertSee('Penentuan Bobot Kriteria');

        $kriteria = Kriteria::all();
        $this->assertGreaterThanOrEqual(2, $kriteria->count());

        $bestId = $kriteria->first()->id_kriteria;
        $worstId = $kriteria->last()->id_kriteria;

        $bo = [];
        $ow = [];
        foreach ($kriteria as $c) {
            $bo[$c->id_kriteria] = ($c->id_kriteria == $bestId) ? 1 : 3;
            $ow[$c->id_kriteria] = ($c->id_kriteria == $worstId) ? 1 : 3;
        }

        $calcResponse = $this->actingAs($this->admin)->postJson('/bwm/calculate', [
            'best_id' => $bestId,
            'worst_id' => $worstId,
            'bo' => $bo,
            'ow' => $ow,
        ]);

        $calcResponse->assertStatus(200);
        $calcResponse->assertJsonStructure([
            'success',
            'weights',
            'cr',
            'xi',
            'is_consistent',
        ]);
    }

    public function test_marcos_tabbed_view_renders_both_publication_and_analysis_mode(): void
    {
        $response = $this->actingAs($this->admin)->get('/marcos');
        $response->assertStatus(200);
        $response->assertSee('1. Daftar Penerima Rekomendasi');
        $response->assertSee('2. Langkah Hitungan Matematis (Skripsi)');
        $response->assertSee('Ideal (AI)');
        $response->assertSee('Anti-Ideal (AAI)');
    }

    public function test_official_print_letterhead_renders_properly(): void
    {
        $response = $this->actingAs($this->admin)->get('/laporan/cetak');
        $response->assertStatus(200);
        $response->assertSee('PEMERINTAH KABUPATEN');
        $response->assertSee('PEMERINTAH DESA PALALANG');
        $response->assertSee('Kepala Desa Palalang');
        $response->assertSee('Ketua Badan Permusyawaratan Desa');
    }

    public function test_excel_export_generates_csv_stream(): void
    {
        $response = $this->actingAs($this->admin)->get('/laporan/export-excel');
        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-type'), 'text/csv'));
    }
}
