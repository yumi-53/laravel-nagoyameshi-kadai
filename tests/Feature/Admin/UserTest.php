<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    //未ログインのユーザーは管理者側の会員一覧ページにアクセスできない
    public function test_guest_cannot_access_admin_users_index()
    {
        $response = $this->get('/admin/users/index');
        $response->assertRedirect('/admin/login');
    }
    //ログイン済みの一般ユーザーは管理者側の会員一覧ページにアクセスできない
    public function test_user_cannot_access_admin_users_index()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin/users/index');
        $response->assertRedirect('/admin/login');
    }
    //ログイン済みの管理者は管理者側の会員一覧ページにアクセスできる
    public function test_admin_can_access_admin_users_index()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $response = $this->actingAs($admin, 'admin')->get('/admin/users/index');
        $response->assertStatus(200);
    }

    //未ログインのユーザーは管理者側の会員詳細ページにアクセスできない
    public function test_guest_cannot_access_admin_users_show()
    {
        $user = User::factory()->create();
        $response = $this->get(route('admin.users.show', $user));
        $response->assertRedirect('/admin/login');
    }
    //ログイン済みの一般ユーザーは管理者側の会員詳細ページにアクセスできない
    public function test_user_cannot_access_admin_users_show()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('admin.users.show', $user));
        $response->assertRedirect('/admin/login');
    }
    //ログイン済みの管理者は管理者側の会員詳細ページにアクセスできる
    public function test_admin_can_access_admin_users_show()
    {
        $user = User::factory()->create();
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $response = $this->actingAs($admin, 'admin')->get(route('admin.users.show', $user));
        $response->assertStatus(200);

    }
}
