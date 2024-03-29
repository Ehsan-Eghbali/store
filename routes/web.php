<?php

    use App\Http\Controllers\BrandController;
    use App\Http\Controllers\CategoryController;
    use App\Http\Controllers\ProductController;
    use Illuminate\Support\Facades\Route;

    /*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application. These
    | routes are loaded by the RouteServiceProvider and all of them will
    | be assigned to the "web" middleware group. Make something great!
    |
    */

    Route::resources([
        'brand' => BrandController::class,
        'category' => CategoryController::class,
        'product' => ProductController::class,
    ]);
    Route::get('/token', function () {
        return class_exists('App\Models\Brand');
//        return csrf_token();
    });
    Route::get('/products/search', [ProductController::class, 'search']);



Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
