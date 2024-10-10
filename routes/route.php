<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\FavoriteController;

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
    Route::get('home', [Admin\HomeController::class, 'index'])->name('home');
    Route::get('users/index', [Admin\UserController::class, 'index'])->name('users.index');
    Route::get('users/show/{id}', [Admin\UserController::class, 'show'])->name('users.show');

    Route::resource('restaurants', Admin\RestaurantController::class);
    Route::patch('restaurants/update/{id}',[Admin\RestaurantController::class, 'update'])->name('admin.restaurants.update');

    Route::resource('categories', Admin\CategoryController::class);

    Route::resource('company', Admin\CompanyController::class);

    Route::resource('terms', Admin\TermController::class);
});

Route::group(['middleware' => 'guest:admin'], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::resource('user', UserController::class)->only(['index', 'edit', 'update'])->middleware(['auth', 'verified'])->names('user');
    Route::resource('restaurants', RestaurantController::class)->only(['index', 'show']);
    
    Route::get('restaurants/{restaurant}/reviews', [ReviewController::class, 'index'])->middleware(['auth', 'verified'])->name('restaurants.reviews.index');

    Route::group(['middleware' => ['auth', 'verified', 'subscribed']], function () {
        Route::resource('restaurants/{restaurant}/reviews', ReviewController::class)->except(['index'])->names('restaurants.reviews');  
    
        Route::resource('reservations', ReservationController::class)->only(['index', 'destroy'])->names('reservations');
        Route::resource('restaurants/{restaurant}/reservations', ReservationController::class)->only(['create', 'store'])->names('restaurants.reservations');

        Route::get('favorites', [FavoriteController::class, 'index'])->name('favorites.index');
        Route::post('favorites/{restaurant_id}', [FavoriteController::class, 'store'])->name('favorites.store');
        Route::delete('favorites/{restaurant_id}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
    });

    Route::group(['prefix' => 'subscription', 'as' => 'subscription.'], function () {
        Route::group(['middleware' => ['auth', 'verified', 'not.subscribed']], function () {
            Route::get('create', [SubscriptionController::class, 'create'])->name('create');
            Route::post('/', [SubscriptionController::class, 'store'])->name('store');
        });
        Route::group(['middleware' => ['auth', 'verified', 'subscribed']], function () {
            Route::get('edit', [SubscriptionController::class, 'edit'])->name('edit');
            Route::patch('update', [SubscriptionController::class, 'update'])->name('update');
            Route::get('cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
            Route::delete('/', [SubscriptionController::class, 'destroy'])->name('destroy');
        });
    });
}); 
?>