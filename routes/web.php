<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\HomeController;

use App\Http\Controllers\MaterialController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
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

Route::get('/', function () {
    return Redirect::route('login.login');
});


Route::name('login.')->group(function () {
    Route::get('login',[LoginController::class,'show'])->name('login');
    Route::post('perform',[LoginController::class,'login'])->name('perform');
    Route::get('logout',[LoginController::class,'logout'])->name('logout');
});

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard',[HomeController::class,'index'])->name('dashboard');
    Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('users');
    Route::get('/users/get', [App\Http\Controllers\UserController::class, 'get'])->name('users.get');
    Route::get('/users/create', [App\Http\Controllers\UserController::class, 'create'])->name('users.create');
    Route::post('/users/store', [App\Http\Controllers\UserController::class, 'store'])->name('users.store');  
    Route::get('/users/edit/{id}', [App\Http\Controllers\UserController::class, 'edit'])->name('users.edit');  
    Route::post('/users/update/{id}', [App\Http\Controllers\UserController::class, 'update'])->name('users.update'); 

    Route::get('orders',[OrderController::class,'index'])->name('orders');
    Route::get('orders/get/{type?}',[OrderController::class,'get'])->name('orders.get');
    Route::get('orders/create/{id?}',[OrderController::class,'create'])->name('orders.create');
    Route::get('orders/detail/{id?}',[OrderController::class,'detail'])->name('orders.detail');
    
    Route::post('orders/store/{id?}/{type?}',[OrderController::class,'store'])->name('orders.store');
    //Route::get('product',[MaterialController::class,'index'])->name('product');


    //Route::view('product', 'ecommerce.product')->name('product');
    //Route::view('add_product', 'ecommerce.add_product')->name('add_product');
    //Route::view('page-product', 'ecommerce.product_page')->name('page-product');
    //Route::view('list-products', 'ecommerce.list_products')->name('list-products');
    //Route::view('payment-details', 'ecommerce.payment_details')->name('payment-details');
   // Route::view('order-history', 'ecommerce.order_history')->name('order-history');
   // Route::view('invoice-template', 'ecommerce.invoice_template')->name('invoice-template');
    //Route::view('cart', 'ecommerce.cart')->name('cart');
    //Route::view('list-wish', 'ecommerce.list_wish')->name('list-wish');
    //Route::view('checkout', 'ecommerce.checkout')->name('checkout');
    //Route::view('pricing', 'ecommerce.pricing')->name('pricing');
});


