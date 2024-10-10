<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use App\Models\Term;
use Illuminate\Support\Facades\Hash;

class TermTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    // 未ログインのユーザーは会員側の利用規約ページにアクセスできる
    public function test_guest_can_access_term()
    {
        Term::factory()->create();
        $response = $this->get('/terms');
        $response->assertOk();
    }

    // ログイン済みの一般ユーザーは会員側の利用規約ページにアクセスできる
    public function test_user_can_access_term()
    {
        Term::factory()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/terms');
        $response->assertOk();
    }

    // ログイン済みの管理者は会員側の利用規約ページにアクセスできない
    public function test_admin_cannot_access_term()
    {
        Term::factory()->create();
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $response = $this->actingAs($admin, 'admin')->get('/terms');
        $response->assertStatus(302);
    }
}
