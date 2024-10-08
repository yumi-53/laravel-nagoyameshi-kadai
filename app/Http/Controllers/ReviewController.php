<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\ReviewRequest;

class ReviewController extends Controller
{
    public function index(Restaurant $restaurant)
    {
        //$restaurant = Restaurant::find(id);
        $user = Auth::user();
        if ($user->subscribed('premium_plan')) {
            $reviews = Review::where('restaurant_id', $restaurant->id)
                            ->orderBy('created_at', 'desc')
                            ->paginate(config('view.paidmember_reviews')); 
        } else {
            $reviews = Review::where('restaurant_id', $restaurant->id)
                            ->orderBy('created_at', 'desc')
                            ->paginate(config('view.freemember_reviews'));
        }

        return view('reviews/index', compact('restaurant', 'reviews'));
    }


    public function create(Restaurant $restaurant)
    {
        return view('reviews/create', compact('restaurant'));
    }


    public function store(ReviewRequest $request, Restaurant $restaurant) : RedirectResponse
    {
        Review::create([
            'score' => $request->input('score'),
            'content' =>  $request->input('content'),
            'restaurant_id' => $restaurant->id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('restaurants.reviews.index')->with('flash_message', 'レビューを投稿しました。');
    }

    public function edit(Restaurant $restaurant, Review $review)
    {
        $auth_id = Auth::id();
        if ($auth_id == $review->user_id) {
            return view('reviews/edit', compact('restaurant', 'reviews'));
        } else {
            return redirect()->route('restaurants.reviews.index')->with('error_message', '不正なアクセスです。');
        }
    }


    public function update(ReviewRequest $request): RedirectResponse
    {
        $auth_id = Auth::id();
        if ($auth_id == $request->user_id) {
            Review::updateOrCreate([
                'score' => $request->input('score'),
                'content' =>  $request->input('content'),
            ]);
            return redirect()->route('restaurants.reviews.index')->with('flash_message', 'レビューを編集しました。');

        } else {
            return redirect()->route('restaurants.reviews.index')->with('error_message', '不正なアクセスです。');
        }
    }


    public function destroy(Restaurant $restaurant, Review $review)
    {
        $auth_id = Auth::id();
        if ($auth_id == $review->user_id) {
            $restaurant->delete();
            return redirect()->route('restaurants.reviews.index')->with('flash_message', 'レビューを削除しました。');
        } else {
            return redirect()->route('restaurants.reviews.index')->with('error_message', '不正なアクセスです。');
        }
    }

}
