<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventoryController;
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
Route::get('/clear-cache', function () {
    try {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');

        return "Cache cleared successfully.";
    } catch (\Exception $e) {
        return "Failed to clear cache: " . $e->getMessage();
    }
});
Route::middleware(['auth'])->group(function () {
    Route::get('dashboard',[HomeController::class,'index'])->name('dashboard');
    Route::get('/estadistica/{status?}/{rol?}',[HomeController::class,'estadistica'])->name('dashboard.estadistica');
    Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('users');
    Route::get('/users/get', [App\Http\Controllers\UserController::class, 'get'])->name('users.get');
    Route::get('/users/create', [App\Http\Controllers\UserController::class, 'create'])->name('users.create');
    Route::post('/users/store', [App\Http\Controllers\UserController::class, 'store'])->name('users.store');  
    Route::get('/users/edit/{id}', [App\Http\Controllers\UserController::class, 'edit'])->name('users.edit');  
    Route::get('/users/profile/', [App\Http\Controllers\UserController::class, 'profile'])->name('users.profile');  
    Route::post('/users/update/{id}', [App\Http\Controllers\UserController::class, 'update'])->name('users.update'); 

    Route::get('orders',[OrderController::class,'index'])->name('orders');
    Route::get('orders/prueba',[OrderController::class,'prueba'])->name('orders.prueba');
    Route::get('orders/prueba-orders',[OrderController::class,'pruebaOrders'])->name('orders.pruebaorders');
    Route::get('orders/get/{type?}',[OrderController::class,'get'])->name('orders.get');
    Route::get('orders/create/{id?}',[OrderController::class,'create'])->name('orders.create');
    Route::get('orders/detail/{id?}',[OrderController::class,'detail'])->name('orders.detail');
    Route::get('orders/estadistica/{type?}',[OrderController::class,'estadistica'])->name('orders.estadistica'); 
    Route::post('orders/store/{id?}/{type?}',[OrderController::class,'store'])->name('orders.store');
    //Route::get('product',[MaterialController::class,'index'])->name('product');
    Route::get('orders/web',[OrderController::class,'get_orders'])->name('orders.get_orders');
    Route::get('orders/web-completados',[OrderController::class,'get_orders_completed'])->name('orders.get_orders_completed');
    Route::get('orders/web-datatable',[OrderController::class,'get_orders_datatable'])->name('orders.get_orders_datatable');
    Route::get('orders/sync-invoices',[OrderController::class,'sync_invoices'])->name('orders.sync_invoices');
    Route::get('orders/qr/{id}', [OrderController::class,'getQrCode'])->name('orders.qr');
    Route::get('orders/qr-validation/{order_id}', [OrderController::class,'redirectToDetail'])->name('orders.qr_validation');
    Route::get('orders/pdf/{idOrder}', [OrderController::class,'getPdfOrder'])->name('orders.pdf');
    Route::post('orders/generate-qr-selected',  [OrderController::class,'generateQrSelected'])->name('orders.generate_qr_selected');
    Route::post('orders/pdf-qr-masivos', [OrderController::class,'generatePdfMultiple'])->name('orders.pdf_qr_masivos');
    Route::get('orders/siigo-factura/{id_order}', [OrderController::class,'getinvoiceSiigo'])->name('orders.invoicesiigo');



    Route::get('inventario',[InventoryController::class,'index'])->name('inventory.index');
    Route::get('inventario/respuesta/{id?}',[InventoryController::class,'search'])->name('inventory.search');
    Route::post('inventario/actualizar-sctock',[InventoryController::class,'updateStock'])->name('inventory.updateStock');

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


