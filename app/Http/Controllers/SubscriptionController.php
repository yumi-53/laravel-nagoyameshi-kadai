<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function create()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.home');
        }
        
        $intent = Auth::user()->createSetupIntent();
        return view('subscription.create', compact('intent'));
    }

    public function store(Request $request)
    {   
        Route::get('/subscription-checkout', function (Request $request) {
            return $request->user()
                ->newSubscription('premium_plan', 'price_1Q56Jz2KgC8QW3q7h76nyfTz')
                ->trialDays(5)
                ->allowPromotionCodes()
                ->checkout([
                    'success_url' => route('your-success-route'),
                    'cancel_url' => route('your-cancel-route'),
                ]);
        });

        return redirect()->route('user.index', compact('user'))->with('flash_message', '有料プランへの登録が完了しました。');
    }

    public function edit()
    {
        $user = Auth::user();
        $intent = Auth::user()->createSetupIntent();

        return view('subscription/edit', compact('user','intent'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $user->updateDefaultPaymentMethod($request->paymentMethodId);

        return redirect()->route('user.index')->with('flash_message', '支払い方法を変更しました。');
    }

    
    public function cancel()
    {
        return view('subscription.cancel');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->subscription('default')->canceled()) {
            return redirect()->route('user.index')->with('flash_message', '有料プランを解約しました。');
        }
    }
}
