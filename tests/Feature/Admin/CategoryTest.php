<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use App\Models\Category;


class CategoryTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    //未ログインのユーザーは管理者側のカテゴリ一覧ページにアクセスできない
    public function test_guest_cannot_access_admin_users_categories_index()
    {
        $response = $this->get('/admin/categories');
        $response->assertRedirect('/admin/login');
    }
    //ログイン済みの一般ユーザーは管理者側のカテゴリ一覧ページにアクセスできない
    public function test_user_cannot_access_admin_users_categories_index()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin/categories');
        $response->assertRedirect('/admin/login');
    }
    //ログイン済みの管理者は管理者側のカテゴリ一覧ページにアクセスできる
    public function test_admin_can_access_admin_users_categories_index()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $response = $this->actingAs($admin, 'admin')->get('/admin/categories');
        $response->assertOk();
    }


    //未ログインのユーザーはカテゴリを登録できない
    public function test_guest_cannot_registration_categories()
    {
        $category = [
            'name' => 'TEST',
        ];
        $this->post(route('admin.categories.store'), $category);
        $this->assertDatabaseMissing('categories', $category);
    }
    //ログイン済みの一般ユーザーはカテゴリを登録できない
    public function test_user_cannot_registration_categories()
    {
        $category = [
            'name' => 'TEST',
        ];
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('admin.categories.store'), $category);
        $this->assertDatabaseMissing('categories', $category);
    }
    //ログイン済みの管理者はカテゴリを登録できる
    public function test_admin_can_registration_categories()
    {
        $category = [
            'name' => 'TEST',
        ];
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $this->actingAs($admin, 'admin')->post(route('admin.categories.store'), $category);
        $this->assertDatabaseHas('categories', $category);
    }


    //未ログインのユーザーはカテゴリを更新できない
    public function test_guest_cannot_update_categories()
    {
        $old_category = Category::factory()->create();
        $new_category = [
            'name' => 'TEST2',
        ];
        $this->patch(route('admin.categories.update', $old_category), $new_category);
        $this->assertDatabaseMissing('categories', $new_category);
    }
    //ログイン済みの一般ユーザーはカテゴリを更新できない
    public function test_user_cannot_update_categories()
    {
        $old_category = Category::factory()->create();
        $new_category = [
            'name' => 'TEST2',
        ];
        $user = User::factory()->create();
        $this->actingAs($user)->patch(route('admin.categories.update', $old_category), $new_category);
        $this->assertDatabaseMissing('categories', $new_category);
    }
    //ログイン済みの管理者はカテゴリを更新できる
    public function test_admin_can_update_categories()
    {
        $old_category = Category::factory()->create();
        $new_category = [
            'name' => 'TEST2',
        ];
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $this->actingAs($admin, 'admin')->patch(route('admin.categories.update', $old_category), $new_category);
        $this->assertDatabaseHas('categories', $new_category);
    }


    //未ログインのユーザーはカテゴリを削除できない
    public function test_guest_cannot_delete_categories()
    {
        $category = Category::factory()->create();
        $delete_id = [
            'id' => $category->id,
        ];
        $this->delete(route('admin.categories.destroy', $category));
        $this->assertDatabaseHas('categories', $delete_id);
    }
    //ログイン済みの一般ユーザーはカテゴリを削除できない
    public function test_user_cannot_delete_categories()
    {
        $category = Category::factory()->create();
        $delete_id = [
            'id' => $category->id,
        ];
        $user = User::factory()->create();
        $this->actingAs($user)->delete(route('admin.categories.destroy', $category));
        $this->assertDatabaseHas('categories', $delete_id);
    }
    //ログイン済みの管理者はカテゴリを削除できる
    public function test_admin_can_delete_categories()
    {
        $category = Category::factory()->create();
        $delete_id = [
            'id' => $category->id,
        ];
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $this->actingAs($admin, 'admin')->delete(route('admin.categories.destroy', $category));
        $this->assertDatabaseMissing('categories', $delete_id);
    }
}
