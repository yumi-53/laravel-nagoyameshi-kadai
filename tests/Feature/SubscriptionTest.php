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
    public function test_free_member_cannot_access_subscription_create()
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
    // public function test_admin_cannot_access_subscription_create()
    // {
    //     $admin = Admin::create([
    //         'email' => 'admin@example.com',
    //         'password' => Hash::make('nagoyameshi'),
    //     ]);
    //     $response = $this->actingAs($admin, 'admin')->get('subscription/create');
    //     $response->assertRedirect(route('admin.home'));
    // }


    //未ログインのユーザーは有料プランに登録できない
    public function test_guest_cannot_registration_subscription_store()
    {
        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];
        $response = $this->post(route('subscription.store'), $request_parameter);
        $response->assertRedirect('/login');
    }
    //ログイン済みの無料会員は有料プランに登録できる
    public function test_free_member_cannot_registration_subscription_store()
    {
        $user = User::factory()->create();
        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];
        $this->actingAs($user)->post(route('subscription.store'), $request_parameter);
        $this->assertTrue($user->subscribed('premium_plan'));
    }
}
