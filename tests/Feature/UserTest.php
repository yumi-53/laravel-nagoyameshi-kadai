<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    //未ログインのユーザーは会員側の会員情報ページにアクセスできない
    public function test_guest_cannot_access_users_index()
    {
        $response = $this->get('/user');
        $response->assertRedirect('/login');
    }
    //ログイン済みの一般ユーザーは会員側の会員情報ページにアクセスできる
    public function test_user_can_access_users_index()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/user');
        $response->assertOk();
    }
    //ログイン済みの管理者は管理者側の会員情報ページにアクセスできない
    public function test_admin_cannot_access_users_index()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $response = $this->actingAs($admin, 'admin')->get('/user');
        $response->assertRedirect('/admin/home');
    }


    //未ログインのユーザーは会員側の会員情報編集ページにアクセスできない
    public function test_guest_cannot_access_users_edit()
    {
        $user = User::factory()->create();
        $response = $this->get(route('user.edit', $user));
        $response->assertRedirect('/login');
    }
    //ログイン済みの一般ユーザーは会員側の他人の会員情報編集ページにアクセスできない
    public function test_user_cannot_access_other_users_show()
    {
        $dummy_user = User::factory()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('user.edit', $dummy_user));
        $response->assertRedirect('/user');
    }
    //ログイン済みの一般ユーザーは会員側の自身の会員情報編集ページにアクセスできる
    public function test_user_can_access_users_edit()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('user.edit', $user));
        $response->assertOk();
    }
    //ログイン済みの管理者は会員側の会員情報編集ページにアクセスできない
    public function test_admin_cannot_access_users_edit()
    {
        $user = User::factory()->create();
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $response = $this->actingAs($admin, 'admin')->get(route('user.edit', $user));
        $response->assertRedirect('/admin/home');
    }


    //未ログインのユーザーは会員情報を更新できない
    public function test_guest_cannot_access_users_update()
    {
        $old_data = User::factory()->create();
        $new_data = User::factory()->create()->toArray();
        $this->patch(route('user.update', $old_data), $new_data);
        $this->assertDatabaseMissing('users', $new_data);

    }
    //ログイン済みの一般ユーザーは会員側の他人の会員情報を更新できない
    public function test_user_cannot_access_other_users_update()
    {
        $dummy_user = User::factory()->create();
        $user = User::factory()->create();
        $new_data = User::factory()->create()->toArray();;
        $this->actingAs($user)->patch(route('user.edit', $dummy_user), $new_data);
        $this->assertDatabaseMissing('users', $new_data);
    }
    //ログイン済みの一般ユーザーは会員側の自身の会員情報を更新できる
    public function test_user_can_access_users_update()
    {
        $user = User::factory()->create();
        $new_data = ([
            'name' => 'test',
            'kana' => 'テスト',
            'email' => 'admin@example.com',
            'postal_code' => 1234567,
            'address' => '大阪府',
            'phone_number' => 1111111111,
        ]);
        $this->actingAs($user)->patch(route('user.update', $user), $new_data);
        $this->assertDatabaseHas('users', $new_data);
    }

    //ログイン済みの管理者は会員情報を更新できない
    public function test_admin_cannot_access_users_update()
    {
        $user = User::factory()->create();
        $new_data = User::factory()->make()->toArray();
        unset($new_data['id'], $new_data['created_at'], $new_data['updated_at'], $new_data['email_verified_at']);
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $this->actingAs($admin, 'admin')->patch(route('user.update', $user), $new_data);
        $this->assertDatabaseMissing('users', $new_data);
    }

}
