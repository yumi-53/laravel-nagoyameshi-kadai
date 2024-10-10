<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;

class CompanyTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    // 未ログインのユーザーは会員側の会社概要ページにアクセスできる
    public function test_guest_can_access_company()
    {
        Company::factory()->create();
        $response = $this->get('/company');
        $response->assertOk();
    }
    // ログイン済みの一般ユーザーは会員側の会社概要ページにアクセスできる
    public function test_member_can_access_company()
    {
        Company::factory()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/company');
        $response->assertOk();
    }
    // ログイン済みの管理者は会員側の会社概要ページにアクセスできない
    public function test_admin_cannot_access_company()
    {
        Company::factory()->create();
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $response = $this->actingAs($admin, 'admin')->get('/company');
        $response->assertStatus(302);
    }
}
