<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Company;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class CompanyTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;

    //未ログインのユーザーは管理者側の会社概要ページにアクセスできない
    public function test_guest_cannot_access_admin_users_company_index()
    {
        Company::factory()->create();
        $response = $this->get('/admin/company');
        $response->assertRedirect('/admin/login');
    }
    //ログイン済みの一般ユーザーは管理者側の会社概要ページにアクセスできない
    public function test_user_cannot_access_admin_users_company_index()
    {
        Company::factory()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin/company');
        $response->assertRedirect('/admin/login');
    }
    //ログイン済みの管理者は管理者側の会社概要ページにアクセスできる
    public function test_admin_can_access_admin_users_company_index()
    {
        Company::factory()->create();
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $response = $this->actingAs($admin, 'admin')->get('/admin/company');
        $response->assertOk();
    }

    
    //未ログインのユーザーは管理者側の会社概要編集ページにアクセスできない
    public function test_guest_cannot_access_admin_users_company_edit()
    {
        $company = Company::factory()->create();
        $response = $this->get(route('admin.company.edit', $company));
        $response->assertRedirect('/admin/login');
    }
    //ログイン済みの一般ユーザーは管理者側の会社概要編集ページにアクセスできない
    public function test_user_cannot_access_admin_users_company_edit()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('admin.company.edit', $company));
        $response->assertRedirect('/admin/login');
    }
    //ログイン済みの管理者は管理者側の会社概要編集ページにアクセスできる
    public function test_admin_can_access_admin_users_company_edit()
    {
        $company = Company::factory()->create();
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $response = $this->actingAs($admin, 'admin')->get(route('admin.company.edit', $company));
        $response->assertOk();
    }


     //未ログインのユーザーは会社概要を更新できない
    public function test_guest_cannot_update_company()
    {
        $old_company = Company::factory()->create();
        $new_company = [
            'name' => 'TEST2',
            'postal_code' => '0000000',
            'address' => 'テスト',
            'representative' => 'テスト',
            'establishment_date' => 'テスト',
            'capital' => 'テスト',
            'business' => 'テスト',
            'number_of_employees' => 'テスト',
        ];
        $this->patch(route('admin.company.update', $old_company), $new_company);
        $this->assertDatabaseMissing('companies', $new_company);
    }
    //ログイン済みの一般ユーザーは会社概要を更新できない
    public function test_user_cannot_update_company()
    {
        $old_company = Company::factory()->create();
        $new_company = [
            'name' => 'TEST2',
            'postal_code' => '0000000',
            'address' => 'テスト',
            'representative' => 'テスト',
            'establishment_date' => 'テスト',
            'capital' => 'テスト',
            'business' => 'テスト',
            'number_of_employees' => 'テスト',
        ];
        $user = User::factory()->create();
        $this->actingAs($user)->patch(route('admin.company.update', $old_company), $new_company);
        $this->assertDatabaseMissing('companies', $new_company);
    }
    //ログイン済みの管理者は会社概要を更新できる
    public function test_admin_can_update_company()
    {
        $old_company = Company::factory()->create();
        $new_company = [
            'name' => 'TEST2',
            'postal_code' => '0000000',
            'address' => 'テスト',
            'representative' => 'テスト',
            'establishment_date' => 'テスト',
            'capital' => 'テスト',
            'business' => 'テスト',
            'number_of_employees' => 'テスト',
        ];
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $this->actingAs($admin, 'admin')->patch(route('admin.company.update', $old_company), $new_company);
        $this->assertDatabaseHas('companies', $new_company);
    }
}
