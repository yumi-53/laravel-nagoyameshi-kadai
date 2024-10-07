<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    
    //未ログインのユーザーは有料プラン登録ページにアクセスできない
    public function test_guest_cannot_access_subscription_create()
    {
        $response = $this->get('/subscription/create');
        $response->assertRedirect('/login');
    }
    //ログイン済みの無料会員は有料プラン登録ページにアクセスできる
    public function test_free_member_can_access_subscription_create()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/subscription/create');
        $response->assertOk();
    }
    //ログイン済みの有料会員は有料プラン登録ページにアクセスできない
    public function test_paid_member_cannot_access_subscription_create()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q56Jz2KgC8QW3q7h76nyfTz')->create('pm_card_visa');
        $response = $this->actingAs($user)->get('subscription/create');
        
        $response->assertRedirect(route('subscription.edit'));
    }
    //ログイン済みの管理者は有料プラン登録ページにアクセスできない
    public function test_admin_cannot_access_subscription_create()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $response = $this->actingAs($admin, 'admin')->get('subscription/create');
        $response->assertRedirect(route('admin.home'));
    }


    //未ログインのユーザーは有料プランに登録できない
    public function test_guest_cannot_registration_subscription_paid_plan()
    {
        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];
        $response = $this->post(route('subscription.store'), $request_parameter);
        $response->assertRedirect('/login');
    }
    //ログイン済みの無料会員は有料プランに登録できる
    public function test_free_member_can_registration_subscription_paid_plan()
    {
        $user = User::factory()->create();
        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];
        $this->actingAs($user)->post(route('subscription.store'), $request_parameter);
        $user->refresh();
        $this->assertTrue($user->subscribed('premium_plan'));
    }
    //ログイン済みの有料会員は有料プランに登録できない
    public function test_paid_member_cannot_registration_subscription_paid_plan()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q56Jz2KgC8QW3q7h76nyfTz')->create('pm_card_visa');
        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];
        $response = $this->actingAs($user)->post(route('subscription.store'), $request_parameter);
        $response->assertRedirect(route('subscription.edit'));
    }
    //ログイン済みの管理者は有料プランに登録できない
    public function test_admin_cannot_registration_subscription_paid_plan()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];
        $response = $this->actingAs($admin, 'admin')->post(route('subscription.store'), $request_parameter);
        $response->assertRedirect(route('admin.home'));
    }


    //未ログインのユーザーはお支払い方法編集ページにアクセスできない
    public function test_guest_cannot_access_subscription_edit()
    {
        $response = $this->get('/subscription/edit');
        $response->assertRedirect('/login');
    }
    //ログイン済みの無料会員はお支払い方法編集ページにアクセスできない
    public function test_free_member_cannot_access_subscription_edit()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/subscription/edit');
        $response->assertRedirect(route('subscription.create'));
    }
    //ログイン済みの有料会員はお支払い方法編集ページにアクセスできる
    public function test_paid_member_can_access_subscription_edit()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q56Jz2KgC8QW3q7h76nyfTz')->create('pm_card_visa');
        $response = $this->actingAs($user)->get('subscription/edit');
        $response->assertOk();
    }
    //ログイン済みの管理者はお支払い方法編集ページにアクセスできない
    public function test_admin_cannot_access_subscription_edit()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $response = $this->actingAs($admin, 'admin')->get('subscription/edit');
        $response->assertRedirect(route('admin.home'));
    }


    //未ログインのユーザーはお支払い方法を更新できない
    public function test_guest_cannot_update_subscription_payment_method()
    {
        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];
        $response = $this->patch(route('subscription.update'), $request_parameter);
        $response->assertRedirect('/login');
    }
    //ログイン済みの無料会員はお支払い方法を更新できない
    public function test_free_member_cannot_update_subscription_payment_method()
    {
        $user = User::factory()->create();
        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];
        $response = $this->actingAs($user)->patch(route('subscription.update'), $request_parameter);
        $response->assertRedirect(route('subscription.create'));
    }
     //ログイン済みの有料会員はお支払い方法を更新できる
    public function test_paid_member_can_update_subscription_payment_method()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q56Jz2KgC8QW3q7h76nyfTz')->create('pm_card_visa');
        $default_payment_method_id = $user->defaultPaymentMethod()->id;
        $request_parameter = [
            'paymentMethodId' => 'pm_card_mastercard'
        ];
        $this->actingAs($user)->patch(route('subscription.update'), $request_parameter);

        $user->refresh();
        $this->assertNotEquals($default_payment_method_id, $user->defaultPaymentMethod()->id);
    }
    //ログイン済みの管理者はお支払い方法を更新できない
    public function test_admin_cannot_update_subscription_payment_method()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];
        $response = $this->actingAs($admin, 'admin')->patch(route('subscription.update'), $request_parameter);
        $response->assertRedirect(route('admin.home'));
    }


    //未ログインのユーザーは有料プラン解約ページにアクセスできない
    public function test_guest_cannot_access_subscription_cancel()
    {
        $response = $this->get('/subscription/cancel');
        $response->assertRedirect('/login');
    }
    //ログイン済みの無料会員は有料プラン解約ページにアクセスできない
    public function test_free_member_cannot_access_subscription_cancel()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/subscription/cancel');
        $response->assertRedirect(route('subscription.create'));
    }
    //ログイン済みの有料会員は有料プラン解約ページにアクセスできる
    public function test_paid_member_can_access_subscription_cancel()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q56Jz2KgC8QW3q7h76nyfTz')->create('pm_card_visa');
        $response = $this->actingAs($user)->get('subscription/cancel');
        $response->assertOk();
    }

    //ログイン済みの管理者は有料プラン解約ページにアクセスできない
    public function test_admin_cannot_access_subscription_cancel()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $response = $this->actingAs($admin, 'admin')->get('subscription/cancel');
        $response->assertRedirect(route('admin.home'));
    }


    //未ログインのユーザーは有料プランを解約できない
    public function test_guest_cannot_cancel_subscription_payment_method()
    {
        $response = $this->delete(route('subscription.destroy'));
        $response->assertRedirect('/login');
    }
    //ログイン済みの無料会員は有料プランを解約できない
    public function test_free_member_cannot_cancel_subscription_payment_method()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete(route('subscription.destroy'));
        $response->assertRedirect(route('subscription.create'));
    }

    //ログイン済みの有料会員は有料プランを解約できる
    public function test_paid_member_can_cancel_subscription_payment_method()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q56Jz2KgC8QW3q7h76nyfTz')->create('pm_card_visa');
        $this->actingAs($user)->delete(route('subscription.destroy', $user));

        $this->assertFalse($user->subscribed('premium_plan'));
    }
    //ログイン済みの管理者は有料プランを解約できない
    public function test_admin_cannot_cancel_subscription_payment_method()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $response = $this->actingAs($admin, 'admin')->delete(route('subscription.destroy'));
        $response->assertRedirect(route('admin.home'));
    }
}