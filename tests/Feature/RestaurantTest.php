<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Hash;

class RestaurantTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    // 未ログインのユーザーは会員側の店舗一覧ページにアクセスできる
    public function test_guest_can_access_restaurants_index()
    {
        $response = $this->get('/restaurants');
        $response->assertOk();
    }
    // ログイン済みの一般ユーザーは会員側の店舗一覧ページにアクセスできる
    public function test_user_can_access_restaurants_index()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/restaurants');
        $response->assertOk();
    }
    // ログイン済みの管理者は会員側の店舗一覧ページにアクセスできない
    public function test_admin_cannot_restaurants_index()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $response = $this->actingAs($admin, 'admin')->get('/restaurants');
        $response->assertRedirect('/admin/home');
    }

    // 未ログインのユーザーは会員側の店舗詳細ページにアクセスできる
    public function test_guest_can_access_restaurants_show()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->get(route('restaurants.show', $restaurant));
        $response->assertOk();
    }
    // ログイン済みの一般ユーザーは会員側の店舗詳細ページにアクセスできる
    public function test_user_can_access_restaurants_show()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('restaurants.show', $restaurant));
        $response->assertOk();
    }
    // ログイン済みの管理者は会員側の店舗詳細ページにアクセスできない
    public function test_admin_cannot_restaurants_show()
    {
        $restaurant = Restaurant::factory()->create();
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $response = $this->actingAs($admin, 'admin')->get(route('restaurants.show', $restaurant));
        $response->assertRedirect('/admin/home');
    }

}
