<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Reservation;
use Illuminate\Support\Facades\Hash;

class FavoriteTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    // 未ログインのユーザーは会員側のお気に入り一覧ページにアクセスできない
    public function test_guest_cannot_access_restaurants_favorites_index()
    {
        $response = $this->get(route('favorites.index'));
        $response->assertRedirect('/login');
    }
    // ログイン済みの無料会員は会員側のお気に入り一覧ページにアクセスできない
    public function test_free_member_cannot_access_restaurants_favorites_index()
    {        
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('favorites.index'));
        $response->assertRedirect('/subscription/create');
    }
    // ログイン済みの有料会員は会員側のお気に入り一覧ページにアクセスできる
    public function test_paid_member_can_access_restaurants_favorites_index()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q56Jz2KgC8QW3q7h76nyfTz')->create('pm_card_visa');
        $response = $this->actingAs($user)->get(route('favorites.index'));
        $response->assertOk();
    }
    // ログイン済みの管理者は会員側のお気に入り一覧ページにアクセスできない
    public function test_admin_cannot_access_restaurants_favorites_index()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $response = $this->actingAs($admin, 'admin')->get(route('favorites.index'));
        $response->assertRedirect(route('admin.home'));
    }


    // 未ログインのユーザーはお気に入りに追加できない
    public function test_guest_cannot_post_restaurants_favorites_store()
    {
        $restaurant = Restaurant::factory()->create();
        $this->post(route('favorites.store', $restaurant->id));
        $this->assertDatabaseMissing('restaurant_user', ['restaurant_id' => $restaurant->id]);
    }

    // ログイン済みの無料会員はお気に入りに追加できない
    public function test_free_member_cannot_post_restaurants_favorites_store()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('favorites.store', $restaurant->id));
        $this->assertDatabaseMissing('restaurant_user', ['restaurant_id' => $restaurant->id]);
    }
    // ログイン済みの有料会員はお気に入りに追加できる
    public function test_paid_member_can_post_restaurants_favorites_store()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q56Jz2KgC8QW3q7h76nyfTz')->create('pm_card_visa');
        $user->favorite_restaurants()->attach($restaurant->id);
        $response = $this->actingAs($user)->post(route('favorites.store', $restaurant->id));

        $response->assertStatus(302);
        $this->assertDatabaseHas('restaurant_user', ['restaurant_id' => $restaurant->id]);
    }
    // ログイン済みの管理者はお気に入りに追加できない
    public function test_admin_cannot_post_restaurants_favorites_store()
    {
        $restaurant = Restaurant::factory()->create();
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $this->actingAs($admin, 'admin')->post(route('favorites.store', $restaurant->id));
        $this->assertDatabaseMissing('restaurant_user', ['restaurant_id' => $restaurant->id]);

    }


    // 未ログインのユーザーはお気に入りを解除できない
    public function test_guest_cannot_post_restaurants_favorites_destroy()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->favorite_restaurants()->attach($restaurant->id);
        $this->delete(route('favorites.destroy', $restaurant->id));
        $this->assertDatabaseHas('restaurant_user', ['restaurant_id' => $restaurant->id]);
    }
    // ログイン済みの無料会員はお気に入りを解除できない
    public function test_free_member_cannot_post_restaurants_favorites_destroy()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->favorite_restaurants()->attach($restaurant->id);
        $this->actingAs($user)->delete(route('favorites.destroy', $restaurant->id));
        $this->assertDatabaseHas('restaurant_user', ['restaurant_id' => $restaurant->id]);
    }
    // ログイン済みの有料会員はお気に入りを解除できる
    public function test_paid_member_can_post_restaurants_favorites_destroy()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q56Jz2KgC8QW3q7h76nyfTz')->create('pm_card_visa');
        $user->favorite_restaurants()->attach($restaurant->id);
        $response = $this->actingAs($user)->delete(route('favorites.destroy', $restaurant->id));
        $response->assertStatus(302);
        $this->assertDatabaseMissing('restaurant_user', ['restaurant_id' => $restaurant->id]);
    }
    // ログイン済みの管理者はお気に入りを解除できない
    public function test_admin_cannot_post_restaurants_favorites_destroy()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->favorite_restaurants()->attach($restaurant->id);
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $this->actingAs($admin, 'admin')->delete(route('favorites.destroy', $restaurant->id));
        $this->assertDatabaseHas('restaurant_user', ['restaurant_id' => $restaurant->id]);

    }
}
