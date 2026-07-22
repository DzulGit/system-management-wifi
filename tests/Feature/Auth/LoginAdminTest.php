<?php

namespace Tests\Feature\Auth;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_bisa_login_dengan_kredensial_benar(): void
    {
        Admin::factory()->operasional()->create([
            'email' => 'operasional@test.com',
            'password' => 'password123',
        ]);

        $response = $this->postJson('/api/admin/login', [
            'email' => 'operasional@test.com',
            'password' => 'password123',
        ]);

        $response->assertOk()->assertJsonStructure(['data' => ['admin', 'token']]);
    }

    public function test_admin_gagal_login_dengan_password_salah(): void
    {
        Admin::factory()->create([
            'email' => 'operasional@test.com',
            'password' => 'password123',
        ]);

        $response = $this->postJson('/api/admin/login', [
            'email' => 'operasional@test.com',
            'password' => 'salah',
        ]);

        $response->assertStatus(422);
    }

    public function test_admin_nonaktif_tidak_bisa_login(): void
    {
        Admin::factory()->create([
            'email' => 'nonaktif@test.com',
            'password' => 'password123',
            'status_aktif' => false,
        ]);

        $response = $this->postJson('/api/admin/login', [
            'email' => 'nonaktif@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422);
    }
}