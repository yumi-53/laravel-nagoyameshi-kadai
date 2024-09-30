<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class HomeTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    //未ログインのユーザーは会員側のトップページにアクセスできる
    public function test_guest_can_access_users_home()
    {
        $response = $this->get('/');
        $response->assertOk();
    }
    //ログイン済みの一般ユーザーは会員側のトップページにアクセスできる
    public function test_user_can_access_users_home()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/');
        $response->assertOk();
    }
    //ログイン済みの管理者は会員側のトップページにアクセスできない
    public function test_admin_cannot_access_users_home()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $response = $this->actingAs($admin, 'admin')->get('/');
        $response->assertRedirect('/admin/home');
    }
}
