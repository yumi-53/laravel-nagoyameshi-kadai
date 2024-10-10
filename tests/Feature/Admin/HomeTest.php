<?php

namespace Tests\Feature\admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class HomeTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    // 未ログインのユーザーは管理者側のトップページにアクセスできない
    public function test_guest_can_access_admin_home()
    {
        $response = $this->get('/admin/home');
        $response->assertStatus(302);
    }
    // ログイン済みの一般ユーザーは管理者側のトップページにアクセスでない
    public function test_member_can_access_admin_home()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin/home');
        $response->assertStatus(302);
    }
    // ログイン済みの管理者は管理者側のトップページにアクセスできる
    public function test_admin_cannot_access_admin_home()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $response = $this->actingAs($admin, 'admin')->get('/admin/home');
        $response->assertOk();
    }
}
