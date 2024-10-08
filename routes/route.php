<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ReviewController;

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
// Route::middleware(['auth', 'verified', 'subscribed'])->group(function () {
//     Route::get('restaurants/{restaurant}/reviews', [ReviewController::class, 'index'])->name('restaurants.reviews.index');
//     Route::get('restaurants/{restaurant}/reviews/create', [ReviewController::class, 'create'])->name('restaurants.reviews.create');
//     Route::post('restaurants/{restaurant}/reviews', [ReviewController::class, 'store'])->name('restaurants.reviews.store');
//     Route::get('restaurants/{restaurant}/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('restaurants.reviews.edit');
//     Route::patch('restaurants/{restaurant}/reviews/{review}', [ReviewController::class, 'update'])->name('restaurants.reviews.update');
//     Route::delete('restaurants/{restaurant}/reviews/{review}', [ReviewController::class, 'destroy'])->name('restaurants.reviews.destroy');

// });

Route::group(['middleware' => 'guest:admin'], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::resource('user', UserController::class)->only(['index', 'edit', 'update'])->middleware(['auth', 'verified'])->names('user');
    Route::resource('restaurants', RestaurantController::class)->only(['index', 'show']);

    Route::resource('restaurants/{restaurant}/reviews', ReviewController::class)->except(['index'])->middleware(['auth', 'verified', 'subscribed'])->names('restaurants.reviews');
    Route::get('restaurants/{restaurant}/reviews', [ReviewController::class, 'index'])->middleware(['auth', 'verified'])->name('restaurants.reviews.index');
    
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