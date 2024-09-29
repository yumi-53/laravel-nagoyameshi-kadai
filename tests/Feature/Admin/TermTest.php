<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Term;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class TermTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    //未ログインのユーザーは管理者側の利用規約ページにアクセスできない
    public function test_guest_cannot_access_admin_users_term_index()
    {
        Term::factory()->create();
        $response = $this->get('/admin/terms');
        $response->assertRedirect('/admin/login');
    }
    //ログイン済みの一般ユーザーは管理者側の利用規約ページにアクセスできない
    public function test_user_cannot_access_admin_users_term_index()
    {
        Term::factory()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin/terms');
        $response->assertRedirect('/admin/login');
    }
    //ログイン済みの管理者は管理者側の利用規約ページにアクセスできる
    public function test_admin_can_access_admin_users_term_index()
    {
        Term::factory()->create();
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $response = $this->actingAs($admin, 'admin')->get('/admin/terms');
        $response->assertOk();
    }

    
    //未ログインのユーザーは管理者側の利用規約編集ページにアクセスできない
    public function test_guest_cannot_access_admin_users_term_edit()
    {
        $term = Term::factory()->create();
        $response = $this->get(route('admin.terms.edit', $term));
        $response->assertRedirect('/admin/login');
    }
    //ログイン済みの一般ユーザーは管理者側の利用規約編集ページにアクセスできない
    public function test_user_cannot_access_admin_users_term_edit()
    {
        $term = Term::factory()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('admin.terms.edit', $term));
        $response->assertRedirect('/admin/login');
    }
    //ログイン済みの管理者は管理者側の利用規約編集ページにアクセスできる
    public function test_admin_can_access_admin_users_term_edit()
    {
        $term = Term::factory()->create();;
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $response = $this->actingAs($admin, 'admin')->get(route('admin.terms.edit', $term));
        $response->assertOk();
    }


     //未ログインのユーザーは会社概要を更新できない
    public function test_guest_cannot_update_company()
    {
        $old_data = Term::factory()->create();
        $new_data = [
            'content' => 'TEST2',
        ];
        $this->patch(route('admin.terms.update', $old_data), $new_data);
        $this->assertDatabaseMissing('terms', $new_data);
    }
    //ログイン済みの一般ユーザーは会社概要を更新できない
    public function test_user_cannot_update_company()
    {
        $old_data = Term::factory()->create();
        $new_data = [
            'content' => 'TEST2',
        ];
        $user = User::factory()->create();
        $this->actingAs($user)->patch(route('admin.terms.update', $old_data), $new_data);
        $this->assertDatabaseMissing('terms', $new_data);
    }
    //ログイン済みの管理者は会社概要を更新できる
    public function test_admin_can_update_company()
    {
        $old_data = Term::factory()->create();
        $new_data = [
            'content' => 'TEST2',
        ];
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $this->actingAs($admin, 'admin')->patch(route('admin.terms.update', $old_data), $new_data);
        $this->assertDatabaseHas('terms', $new_data);
    }
}
