<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CarouselController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    // Route::get('/admin/products', [ProductController::class, 'index'])->name('admin.products');
    Route::prefix('admin')->group(function () {
        Route::prefix('products')->group(function () {
            Route::get('/', [ProductController::class,'index'])->name('products.index');
            Route::post('/store', [ProductController::class,'store'])->name('product.store');
            Route::post('/update', [ProductController::class,'update'])->name('product.update');
            Route::post('/delete', [ProductController::class,'remove'])->name('product.delete');
            Route::post('/sale-activate', [ProductController::class,'activateSale'])->name('product.activate.sale');
            Route::post('/sale-deactivate', [ProductController::class,'deactivateSale'])->name('product.deactivate.sale');
        });
        
        Route::prefix('categories')->group(function () {
            Route::get('/', [CategoryController::class,'index'])->name('categories.index');
            Route::post('/store', [CategoryController::class,'store'])->name('category.store');
            Route::post('/update', [CategoryController::class,'update'])->name('category.update');
            Route::post('/update-status', [CategoryController::class,'editStatus'])->name('category.update.status');
            Route::post('/delete', [CategoryController::class,'remove'])->name('category.delete');
        });

        Route::prefix('brands')->group(function () {
            Route::get('/', [BrandController::class,'index'])->name('brands.index');
            Route::post('/store', [BrandController::class,'store'])->name('brand.store');
            Route::post('/update', [BrandController::class,'update'])->name('brand.update');
            Route::post('/delete', [BrandController::class,'remove'])->name('brand.delete');
        });

        Route::prefix('carousels')->group(function () {
            Route::get('/', [CarouselController::class,'index'])->name('carousels.index');
            Route::post('/store', [CarouselController::class,'store'])->name('carousel.store');
            Route::post('/update', [CarouselController::class,'update'])->name('carousel.update');
            Route::post('/delete', [CarouselController::class,'remove'])->name('carousel.delete');

            Route::prefix('slide')->group(function () {
                Route::post('/store', [CarouselController::class,'storeCarouselSlide'])->name('carousel.slide.store');
                Route::post('/update', [CarouselController::class,'updateCarouselSlide'])->name('carousel.slide.update');
                Route::post('/delete', [CarouselController::class,'removeCarouselSlide'])->name('carousel.slide.remove');
            });
        });
    });

    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');
});

Route::get('phpinfo', function () {
    echo phpinfo();
});