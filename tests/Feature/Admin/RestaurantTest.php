<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Category;
use App\Models\RegularHoliday;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RestaurantTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    //未ログインのユーザーは管理者側の店舗一覧ページにアクセスできない
    public function test_guest_cannot_access_admin_users_restaurants_index()
    {
        $response = $this->get('/admin/restaurants');
        $response->assertRedirect('/admin/login');
    }
    //ログイン済みの一般ユーザーは管理者側の店舗一覧ページにアクセスできない
    public function test_user_cannot_access_admin_users_restaurants_index()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin/restaurants');
        $response->assertRedirect('/admin/login');
    }
    //ログイン済みの管理者は管理者側の店舗一覧ページにアクセスできる
    public function test_admin_can_access_admin_users_restaurants_index()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $response = $this->actingAs($admin, 'admin')->get('/admin/restaurants');
        $response->assertOk();
    }


    //未ログインのユーザーは管理者側の店舗詳細ページにアクセスできない
    public function test_guest_cannot_access_admin_users_restaurants_show()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->get(route('admin.restaurants.store', $restaurant));
        $response->assertRedirect('/admin/login');
    }
    //ログイン済みの一般ユーザーは管理者側の店舗詳細ページにアクセスできない
    public function test_user_cannot_access_admin_users_restaurants_show()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('admin.restaurants.store', $restaurant));
        $response->assertRedirect('/admin/login');
    }
    //ログイン済みの管理者は管理者側の店舗詳細ページにアクセスできる
    public function test_admin_can_access_admin_users_restaurants_show()
    {
        $restaurant = Restaurant::factory()->create();
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.store', $restaurant));
        $response->assertOk();
    }


    //未ログインのユーザーは管理者側の店舗登録ページにアクセスできない
    public function test_guest_cannot_access_admin_users_restaurants_create()
    {
        $response = $this->get('/admin/restaurants/create');
        $response->assertRedirect('/admin/login');
    }
    //ログイン済みの一般ユーザーは管理者側の店舗登録ページにアクセスできない
    public function test_user_cannot_access_admin_users_restaurants_create()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin/restaurants/create');
        $response->assertRedirect('/admin/login');
    }
    //ログイン済みの管理者は管理者側の店舗登録ページにアクセスできる
    public function test_admin_can_access_admin_users_restaurants_create()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $response = $this->actingAs($admin, 'admin')->get('/admin/restaurants/create');
        $response->assertOk();
    }


    //未ログインのユーザーは店舗を登録できない
    public function test_guest_cannot_registration_restaurants()
    {
        $categoryIds = [];
        for ($i = 1; $i <= 3; $i++) {
            $category = Category::create([
                'name' => 'カテゴリ' . $i
            ]);
            array_push($categoryIds, $category->id);    
        }
        RegularHoliday::factory()->count(3)->create();
        $regularHolidayIds = [];
        $regularHolidayIds = RegularHoliday::all()->pluck('id')->toArray();

        $restaurant = [
            'name' => 'TEST',
            'description' => 'TEST',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'TEST',
            'opening_time' => '10:00:00',
            'closing_time' => '20:00:00',
            'seating_capacity' => 50,
            'category_ids' => $categoryIds,
            'regular_holiday_ids' => $regularHolidayIds,
        ];
        $this->post(route('admin.restaurants.store'), $restaurant);
        unset($restaurant['category_ids'], $restaurant['regular_holiday_ids']);
        $this->assertDatabaseMissing('restaurants', $restaurant);

        foreach ($categoryIds as $categoryId) {
            $this->assertDatabaseMissing('category_restaurant', [
                'category_id' => $categoryId,
            ]);
        }

        foreach ($regularHolidayIds as $regularHolidayId) {
            $this->assertDatabaseMissing('regular_holiday_restaurant', [
                'regular_holiday_id' => $regularHolidayId,
            ]);
        }

    }
    //ログイン済みの一般ユーザーは店舗を登録できない
    public function test_user_cannot_registration_restaurants()
    {
        $categoryIds = [];
        for ($i = 1; $i <= 3; $i++) {
            $category = Category::create([
                'name' => 'カテゴリ' . $i
            ]);
            array_push($categoryIds, $category->id);    
        }

        RegularHoliday::factory()->count(3)->create();
        $regularHolidayIds = [];
        $regularHolidayIds = RegularHoliday::all()->pluck('id')->toArray();
        
        $restaurant = [
            'name' => 'TEST',
            'description' => 'TEST',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'TEST',
            'opening_time' => '10:00:00',
            'closing_time' => '20:00:00',
            'seating_capacity' => 50,
            'category_ids' => $categoryIds,
            'regular_holiday_ids' => $regularHolidayIds,
        ];
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('admin.restaurants.store'), $restaurant);
        unset($restaurant['category_ids'], $restaurant['regular_holiday_ids']);
        $this->assertDatabaseMissing('restaurants', $restaurant);

        foreach ($categoryIds as $categoryId) {
            $this->assertDatabaseMissing('category_restaurant', [
                'category_id' => $categoryId,
            ]);
        }

        foreach ($regularHolidayIds as $regularHolidayId) {
            $this->assertDatabaseMissing('regular_holiday_restaurant', [
                'regular_holiday_id' => $regularHolidayId,
            ]);
        }

    }
    //ログイン済みの管理者は店舗を登録できる
    public function test_admin_can_registration_restaurants()
    {
        $categoryIds = [];
        for ($i = 1; $i <= 3; $i++) {
            $category = Category::create([
                'name' => 'カテゴリ' . $i
            ]);
            array_push($categoryIds, $category->id);    
        }

        RegularHoliday::factory()->count(3)->create();
        $regularHolidayIds = [];
        $regularHolidayIds = RegularHoliday::all()->pluck('id')->toArray();

        $restaurant = [
            'name' => 'TEST',
            'description' => 'TEST',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'TEST',
            'opening_time' => '10:00:00',
            'closing_time' => '20:00:00',
            'seating_capacity' => 50,
            'category_ids' => $categoryIds,
            'regular_holiday_ids' => $regularHolidayIds,
        ];
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $this->actingAs($admin, 'admin')->post(route('admin.restaurants.store'), $restaurant);
        unset($restaurant['category_ids'], $restaurant['regular_holiday_ids']);
        $this->assertDatabaseHas('restaurants', $restaurant);

        foreach ($categoryIds as $categoryId) {
            $this->assertDatabaseHas('category_restaurant', [
                'category_id' => $categoryId,
            ]);
        }

        foreach ($regularHolidayIds as $regularHolidayId) {
            $this->assertDatabaseHas('regular_holiday_restaurant', [
                'regular_holiday_id' => $regularHolidayId,
            ]);
        }

    }


    //未ログインのユーザーは管理者側の店舗編集ページにアクセスできない
    public function test_guest_cannot_access_admin_users_restaurants_edit()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->get(route('admin.restaurants.edit', $restaurant));
        $response->assertRedirect('/admin/login');
    }
    //ログイン済みの一般ユーザーは管理者側の店舗編集ページにアクセスできない
    public function test_user_cannot_access_admin_users_restaurants_edit()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('admin.restaurants.edit', $restaurant));
        $response->assertRedirect('/admin/login');
    }
    //ログイン済みの管理者は管理者側の店舗編集ページにアクセスできる
    public function test_admin_can_access_admin_users_restaurants_edit()
    {
        $restaurant = Restaurant::factory()->create();
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.edit', $restaurant));
        $response->assertOk();
    }

    //未ログインのユーザーは店舗を変更できない
    public function test_guest_cannot_update_restaurants()
    {
        $categoryIds = [];
        for ($i = 1; $i <= 3; $i++) {
            $category = Category::create([
                'name' => 'カテゴリ' . $i
            ]);
            array_push($categoryIds, $category->id);    
        }

        RegularHoliday::factory()->count(3)->create();
        $regularHolidayIds = [];
        $regularHolidayIds = RegularHoliday::all()->pluck('id')->toArray();

        $old_restaurant = Restaurant::factory()->create();
        $new_restaurant = [
            'name' => 'TEST2',
            'description' => 'TEST',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'TEST',
            'opening_time' => '10:00:00',
            'closing_time' => '20:00:00',
            'seating_capacity' => 50,
            'category_ids' => $categoryIds,
            'regular_holiday_ids' => $regularHolidayIds,
        ];
        $this->patch(route('admin.restaurants.update', $old_restaurant), $new_restaurant);
        unset($new_restaurant['category_ids'], $new_restaurant['regular_holiday_ids']);
        $this->assertDatabaseMissing('restaurants', $new_restaurant);

        foreach ($categoryIds as $categoryId) {
            $this->assertDatabaseMissing('category_restaurant', [
                'category_id' => $categoryId,
            ]);
        }

        foreach ($categoryIds as $categoryId) {
            $this->assertDatabaseMissing('category_restaurant', [
                'category_id' => $categoryId,
            ]);
        }
    }
    //ログイン済みの一般ユーザーは店舗を変更できない
    public function test_user_cannot_update_restaurants()
    {
        $categoryIds = [];
        for ($i = 1; $i <= 3; $i++) {
            $category = Category::create([
                'name' => 'カテゴリ' . $i
            ]);
            array_push($categoryIds, $category->id);    
        }

        RegularHoliday::factory()->count(3)->create();
        $regularHolidayIds = [];
        $regularHolidayIds = RegularHoliday::all()->pluck('id')->toArray();

        $old_restaurant = Restaurant::factory()->create();
        $new_restaurant = [
            'name' => 'TEST2',
            'description' => 'TEST',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'TEST',
            'opening_time' => '10:00:00',
            'closing_time' => '20:00:00',
            'seating_capacity' => 50,
            'category_ids' => $categoryIds,
            'regular_holiday_ids' => $regularHolidayIds,

        ];
        $user = User::factory()->create();
        $this->actingAs($user)->patch(route('admin.restaurants.update', $old_restaurant), $new_restaurant);
        unset($new_restaurant['category_ids'], $new_restaurant['regular_holiday_ids']);
        $this->assertDatabaseMissing('restaurants', $new_restaurant);

        foreach ($categoryIds as $categoryId) {
            $this->assertDatabaseMissing('category_restaurant', [
                'category_id' => $categoryId,
            ]);
        }

        foreach ($regularHolidayIds as $regularHolidayId) {
            $this->assertDatabaseMissing('regular_holiday_restaurant', [
                'regular_holiday_id' => $regularHolidayId,
            ]);
        }

    }
    //ログイン済みの管理者は店舗を変更できる
    public function test_admin_can_update_restaurants()
    {
        $categoryIds = [];
        for ($i = 1; $i <= 3; $i++) {
            $category = Category::create([
                'name' => 'カテゴリ' . $i
            ]);
            array_push($categoryIds, $category->id);    
        }
        
        RegularHoliday::factory()->count(3)->create();
        $regularHolidayIds = [];
        $regularHolidayIds = RegularHoliday::all()->pluck('id')->toArray();

        $old_restaurant = Restaurant::factory()->create();
        $new_restaurant = [
            'name' => 'TEST2',
            'description' => 'TEST',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'TEST',
            'opening_time' => '10:00:00',
            'closing_time' => '20:00:00',
            'seating_capacity' => 50,
            'category_ids' => $categoryIds,
            'regular_holiday_ids' => $regularHolidayIds,
        ];
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $this->actingAs($admin, 'admin')->patch(route('admin.restaurants.update', $old_restaurant), $new_restaurant);
        unset($new_restaurant['category_ids'], $new_restaurant['regular_holiday_ids']);
        $this->assertDatabaseHas('restaurants', $new_restaurant);

        foreach ($categoryIds as $categoryId) {
            $this->assertDatabaseHas('category_restaurant', [
                'category_id' => $categoryId,
            ]);
        }

        foreach ($regularHolidayIds as $regularHolidayId) {
            $this->assertDatabaseHas('regular_holiday_restaurant', [
                'regular_holiday_id' => $regularHolidayId,
            ]);
        }

    }


    //未ログインのユーザーは店舗を削除できない
    public function test_guest_cannot_delete_restaurants()
    {
        $restaurant = Restaurant::factory()->create();
        $delete_id = [
            'id' => $restaurant->id,
        ];
        $this->delete(route('admin.restaurants.destroy', $restaurant));
        $this->assertDatabaseHas('restaurants', $delete_id);
    }
    //ログイン済みの一般ユーザーは店舗を削除できない
    public function test_user_cannot_delete_restaurants()
    {
        $restaurant = Restaurant::factory()->create();
        $delete_id = [
            'id' => $restaurant->id,
        ];
        $user = User::factory()->create();
        $this->actingAs($user)->delete(route('admin.restaurants.destroy', $restaurant));
        $this->assertDatabaseHas('restaurants', $delete_id);
    }
    //ログイン済みの管理者は店舗を削除できる
    public function test_admin_can_delete_restaurants()
    {
        $restaurant = Restaurant::factory()->create();
        $delete_id = [
            'id' => $restaurant->id,
        ];
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $this->actingAs($admin, 'admin')->delete(route('admin.restaurants.destroy', $restaurant));
        $this->assertDatabaseMissing('restaurants', $delete_id);
    }
    
    
}