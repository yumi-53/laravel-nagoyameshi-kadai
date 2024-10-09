<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Review;
use Illuminate\Support\Facades\Hash;

class ReviewTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    // 未ログインのユーザーは会員側のレビュー一覧ページにアクセスできない
    public function test_guest_cannot_access_restaurants_review_index()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->get(route('restaurants.reviews.index', $restaurant));
        $response->assertRedirect('/login');
    }
    // ログイン済みの無料会員は会員側のレビュー一覧ページにアクセスできる
    public function test_free_member_can_access_restaurants_review_index()
    {        
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('restaurants.reviews.index', $restaurant));
        $response->assertOk();
    }
    // ログイン済みの有料会員は会員側のレビュー一覧ページにアクセスできる
    public function test_paid_member_can_access_restaurants_review_index()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q56Jz2KgC8QW3q7h76nyfTz')->create('pm_card_visa');
        $response = $this->actingAs($user)->get(route('restaurants.reviews.index', $restaurant));
        $response->assertOk();
    }
    // ログイン済みの管理者は会員側のレビュー一覧ページにアクセスできない
    public function test_admin_cannot_access_restaurants_review_index()
    {
        $restaurant = Restaurant::factory()->create();
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $response = $this->actingAs($admin, 'admin')->get(route('restaurants.reviews.index', $restaurant));
        $response->assertRedirect(route('admin.home'));
    }


    // 未ログインのユーザーは会員側のレビュー投稿ページにアクセスできない
    public function test_guest_cannot_access_restaurants_review_create()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->get(route('restaurants.reviews.create', $restaurant));
        $response->assertRedirect('/login');
    }
    // ログイン済みの無料会員は会員側のレビュー投稿ページにアクセスできない
    public function test_free_member_cannot_access_restaurants_review_create()
    {        
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('restaurants.reviews.create', $restaurant));
        $response->assertRedirect('/subscription/create');
    }
    // ログイン済みの有料会員は会員側のレビュー投稿ページにアクセスできる
    public function test_paid_member_can_access_restaurants_review_create()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q56Jz2KgC8QW3q7h76nyfTz')->create('pm_card_visa');
        $response = $this->actingAs($user)->get(route('restaurants.reviews.create', $restaurant));
        $response->assertOk();
    }
    // ログイン済みの管理者は会員側のレビュー投稿ページにアクセスできない
    public function test_admin_cannot_access_restaurants_review_create()
    {
        $restaurant = Restaurant::factory()->create();
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $response = $this->actingAs($admin, 'admin')->get(route('restaurants.reviews.create', $restaurant));
        $response->assertRedirect(route('admin.home'));
    }


    // 未ログインのユーザーはレビューを投稿できない
    public function test_guest_cannot_post_restaurants_review_store()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $review = [
            'score' => 1,
            'content' =>  'test',
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
        ];
        $this->post(route('restaurants.reviews.store', $restaurant), $review);
        $this->assertDatabaseMissing('reviews', $review);
    }
    // ログイン済みの無料会員はレビューを投稿できない
    public function test_free_member_cannot_post_restaurants_review_store()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $review = [
            'score' => 1,
            'content' =>  'test',
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
        ];
        $this->actingAs($user)->post(route('restaurants.reviews.store', $restaurant), $review);
        $this->assertDatabaseMissing('reviews', $review);
    }
    // ログイン済みの有料会員はレビューを投稿できる
    public function test_paid_member_can_post_restaurants_review_store()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q56Jz2KgC8QW3q7h76nyfTz')->create('pm_card_visa');
        $review = [
            'score' => 1,
            'content' =>  'test',
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
        ];
        $this->actingAs($user)->post(route('restaurants.reviews.store', $restaurant), $review);
        $this->assertDatabaseHas('reviews', $review);
    }
    // ログイン済みの管理者はレビューを投稿できない
    public function test_admin_cannot_post_restaurants_review_store()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $review = [
            'score' => 1,
            'content' =>  'test',
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
        ];
        $this->actingAs($admin, 'admin')->post(route('restaurants.reviews.store', $restaurant), $review);
        $this->assertDatabaseMissing('reviews', $review);
    }


    // 未ログインのユーザーは会員側のレビュー編集ページにアクセスできない
    public function test_guest_cannot_access_restaurants_review_edit()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $response = $this->get(route('restaurants.reviews.edit', [$restaurant, $review]));
        $response->assertRedirect('/login');
    }
    // ログイン済みの無料会員はレビュー編集ページにアクセスできない
    public function test_free_member_cannot_access_restaurants_review_edit()
    {        
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $response = $this->actingAs($user)->get(route('restaurants.reviews.edit', [$restaurant, $review]));
        $response->assertRedirect('/subscription/create');
    }
    // ログイン済みの有料会員は会員側の他人のレビュー編集ページにアクセスできない
    public function test_paid_member_cannot_access_other_restaurants_review_edit()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q56Jz2KgC8QW3q7h76nyfTz')->create('pm_card_visa');
        $other_user = User::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $other_user->id
        ]);
        $response = $this->actingAs($user)->get(route('restaurants.reviews.edit', [$restaurant, $review]));
        
        $response->assertRedirect(route('restaurants.reviews.index', $restaurant));
    }
    // ログイン済みの有料会員は会員側の自身のレビュー編集ページにアクセスできる
    public function test_paid_member_can_access_restaurants_review_edit()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q56Jz2KgC8QW3q7h76nyfTz')->create('pm_card_visa');
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $response = $this->actingAs($user)->get(route('restaurants.reviews.edit', [$restaurant, $review]));
        
        $response->assertOk();
    }
    // ログイン済みの管理者は会員側のレビュー編集ページにアクセスできない
    public function test_admin_cannot_access_restaurants_review_edit()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $response = $this->actingAs($admin, 'admin')->get(route('restaurants.reviews.edit', [$restaurant, $review]));
        $response->assertRedirect(route('admin.home'));
    }


    // 未ログインのユーザーはレビューを更新できない
    public function test_guest_cannot_post_restaurants_review_update()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $new_review = [
            'score' => 1,
            'content' =>  'test',
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
        ];
        $this->patch(route('restaurants.reviews.update', [$restaurant, $review]), $new_review);
        $this->assertDatabaseMissing('reviews', $new_review);
    }
    // ログイン済みの無料会員はレビューを更新できない
    public function test_free_member_cannot_post_restaurants_review_update()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $new_review = [
            'score' => 1,
            'content' =>  'test',
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
        ];
        $this->actingAs($user)->patch(route('restaurants.reviews.update', [$restaurant, $review]), $new_review);
        $this->assertDatabaseMissing('reviews', $new_review);
    }
    // ログイン済みの有料会員は他人のレビューを更新できない
    public function test_paid_member_cannot_otherpost_restaurants_review_update()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q56Jz2KgC8QW3q7h76nyfTz')->create('pm_card_visa');
        $other_user = User::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $other_user->id
        ]);
        $new_review = [
            'score' => 1,
            'content' =>  'test',
            'restaurant_id' => $restaurant->id,
            'user_id' => $other_user->id
        ];
        $this->actingAs($user)->patch(route('restaurants.reviews.update', [$restaurant, $review]), $new_review);
        $this->assertDatabaseMissing('reviews', $new_review);
    }
    // ログイン済みの有料会員は自身のレビューを更新できる
    public function test_paid_member_can_post_restaurants_review_update()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q56Jz2KgC8QW3q7h76nyfTz')->create('pm_card_visa');
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $new_review = [
            'score' => 1,
            'content' =>  'test',
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
        ];
        $this->actingAs($user)->patch(route('restaurants.reviews.update', [$restaurant, $review]), $new_review);
        $this->assertDatabaseHas('reviews', $new_review);
    }
    // ログイン済みの管理者はレビューを更新できない
    public function test_admin_cannot_post_restaurants_review_update()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $new_review = [
            'score' => 1,
            'content' =>  'test',
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
        ];
        $this->actingAs($admin, 'admin')->patch(route('restaurants.reviews.update', [$restaurant, $review]), $new_review);
        $this->assertDatabaseMissing('reviews', $new_review);
    }


    // 未ログインのユーザーはレビューを削除できない
    public function test_guest_cannot_post_restaurants_review_destroy()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $review_id = ['id' => $review->id ];
        $this->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));
        $this->assertDatabaseHas('reviews', $review_id);
    }
    // ログイン済みの無料会員はレビューを削除できない
    public function test_free_member_cannot_post_restaurants_review_destroy()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $this->actingAs($user)->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));
        $this->assertDatabaseHas('reviews', ['id' => $review->id ]);
    }
    // ログイン済みの有料会員は他人のレビューを削除できない
    public function test_paid_member_cannot_otherpost_restaurants_review_destroy()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q56Jz2KgC8QW3q7h76nyfTz')->create('pm_card_visa');
        $other_user = User::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $other_user->id
        ]);
        $this->actingAs($user)->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));
        $this->assertDatabaseHas('reviews', ['id' => $review->id ]);
    }
    // ログイン済みの有料会員は自身のレビューを削除できる
    public function test_paid_member_can_post_restaurants_review_destroy()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q56Jz2KgC8QW3q7h76nyfTz')->create('pm_card_visa');
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $this->actingAs($user)->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));
        $this->assertDatabaseMissing('reviews', ['id' => $review->id ]);
    }
    // ログイン済みの管理者はレビューを削除できない
    public function test_admin_cannot_post_restaurants_review_destroy()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $this->actingAs($admin, 'admin')->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));
        $this->assertDatabaseHas('reviews', ['id' => $review->id ]);
    }
}
