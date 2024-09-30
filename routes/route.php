<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\HomeController;

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
});

?>