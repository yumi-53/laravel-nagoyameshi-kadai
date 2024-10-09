<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Review;
use App\Models\Reservation;
use Illuminate\Support\Facades\Hash;

class ReservationTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    // 未ログインのユーザーは会員側の予約一覧ページにアクセスできない
    public function test_guest_cannot_access_restaurants_reservation_index()
    {
        $response = $this->get(route('reservations.index'));
        $response->assertRedirect('/login');
    }
    // ログイン済みの無料会員は会員側の予約一覧ページにアクセスできない
    public function test_free_member_cannot_access_restaurants_reservation_index()
    {        
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('reservations.index'));
        $response->assertRedirect('/subscription/create');
    }
    // ログイン済みの有料会員は会員側の予約一覧ページにアクセスできる
    public function test_paid_member_can_access_restaurants_reservation_index()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q56Jz2KgC8QW3q7h76nyfTz')->create('pm_card_visa');
        $response = $this->actingAs($user)->get(route('reservations.index'));
        $response->assertOk();
    }
    // ログイン済みの管理者は会員側の予約一覧ページにアクセスできない
    public function test_admin_cannot_access_restaurants_reservation_index()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $response = $this->actingAs($admin, 'admin')->get(route('reservations.index'));
        $response->assertRedirect(route('admin.home'));
    }


    // 未ログインのユーザーは会員側の予約ページにアクセスできない
    public function test_guest_cannot_access_restaurants_reservation_create()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->get(route('restaurants.reservations.create', $restaurant));
        $response->assertRedirect('/login');
    }
    // ログイン済みの無料会員は会員側の予約ページにアクセスできない
    public function test_free_member_cannot_access_restaurants_reservation_create()
    {        
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('restaurants.reservations.create', $restaurant));
        $response->assertRedirect('/subscription/create');
    }
    // ログイン済みの有料会員は会員側の予約ページにアクセスできる
    public function test_paid_member_can_access_restaurants_reservation_create()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q56Jz2KgC8QW3q7h76nyfTz')->create('pm_card_visa');
        $response = $this->actingAs($user)->get(route('restaurants.reservations.create', $restaurant));
        $response->assertOk();
    }
    // ログイン済みの管理者は会員側の予約ページにアクセスできない
    public function test_admin_cannot_access_restaurants_reservation_create()
    {
        $restaurant = Restaurant::factory()->create();
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $response = $this->actingAs($admin, 'admin')->get(route('restaurants.reservations.create', $restaurant));
        $response->assertRedirect(route('admin.home'));
    }


    // 未ログインのユーザーは予約できない
    public function test_guest_cannot_post_restaurants_reservation_store()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $reservation = [
            'reservation_date' => '2023-04-16',
            'reservation_time' => '14:00',
            'number_of_people' => 5,
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
        ];
        $this->post(route('restaurants.reservations.store', $restaurant), $reservation);
        $this->assertDatabaseMissing('reservations', ['reserved_datetime' => '2023-04-16 14:00']);
    }
    // ログイン済みの無料会員は予約できない
    public function test_free_member_cannot_post_restaurants_reservation_store()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $reservation = [
            'reservation_date' => '2023-04-16',
            'reservation_time' => '14:00',
            'number_of_people' => 5,
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
        ];
        $this->actingAs($user)->post(route('restaurants.reservations.store', $restaurant), $reservation);
        $this->assertDatabaseMissing('reservations', ['reserved_datetime' => '2023-04-16 14:00']);
    }
    // ログイン済みの有料会員は予約できる
    public function test_paid_member_can_post_restaurants_reservation_store()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q56Jz2KgC8QW3q7h76nyfTz')->create('pm_card_visa');
        $reservation = [
            'reservation_date' => '2023-04-16',
            'reservation_time' => '14:00',
            'number_of_people' => 5,
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
        ];
        $this->actingAs($user)->post(route('restaurants.reservations.store', $restaurant), $reservation);
        $this->assertDatabaseHas('reservations', ['reserved_datetime' => '2023-04-16 14:00']);
    }
    // ログイン済みの管理者は予約できない
    public function test_admin_cannot_post_restaurants_reservation_store()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $reservation = [
            'reservation_date' => '2023-04-16',
            'reservation_time' => '14:00',
            'number_of_people' => 5,
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
        ];
        $this->actingAs($admin, 'admin')->post(route('restaurants.reservations.store', $restaurant), $reservation);
        $this->assertDatabaseMissing('reservations', ['reserved_datetime' => '2023-04-16 14:00']);
    }


    // 未ログインのユーザーは予約をキャンセルできない
    public function test_guest_cannot_post_restaurants_reservation_destroy()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $this->delete(route('reservations.destroy', $reservation));
        $this->assertDatabaseHas('reservations', ['id' => $reservation->id ]);
    }
    // ログイン済みの無料会員は予約をキャンセルできない
    public function test_free_member_cannot_post_restaurants_reservation_destroy()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $this->actingAs($user)->delete(route('reservations.destroy', $reservation));
        $this->assertDatabaseHas('reservations', ['id' => $reservation->id ]);
    }
    // ログイン済みの有料会員は他人の予約をキャンセルできない
    public function test_paid_member_cannot_otherpost_restaurants_reservation_destroy()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q56Jz2KgC8QW3q7h76nyfTz')->create('pm_card_visa');
        $other_user = User::factory()->create();
        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $other_user->id
        ]);
        $this->actingAs($user)->delete(route('reservations.destroy', $reservation));
        $this->assertDatabaseHas('reservations', ['id' => $reservation->id ]);
    }
    // ログイン済みの有料会員は自身の予約をキャンセルできる
    public function test_paid_member_can_post_restaurants_reservation_destroy()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q56Jz2KgC8QW3q7h76nyfTz')->create('pm_card_visa');
        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $this->actingAs($user)->delete(route('reservations.destroy', $reservation));
        $this->assertDatabaseMissing('reservations', ['id' => $reservation->id ]);
    }
    // ログイン済みの管理者は予約をキャンセルできない
    public function test_admin_cannot_post_restaurants_reservation_destroy()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $this->actingAs($admin, 'admin')->delete(route('reservations.destroy', $reservation));
        $this->assertDatabaseHas('reservations', ['id' => $reservation->id ]);
    }
}