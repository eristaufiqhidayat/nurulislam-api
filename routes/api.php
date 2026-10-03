<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageInfoController;
use App\Http\Controllers\MenuRoleController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PembeliController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\DetailPenjualanController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BarangHargaController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\BarangMasukInvController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TabunganQurbanController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ContentController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('pageinfo', [PageInfoController::class, 'index']);

// Konten publik untuk aplikasi Nurul Islam
Route::get('/{category}', [ContentController::class, 'index'])
    ->where('category', 'kegiatan|kajian');
Route::get('/{category}/{id}', [ContentController::class, 'show'])
    ->where('category', 'kegiatan|kajian')
    ->whereNumber('id');
Route::post('register', [AuthController::class, 'register']);
Route::post('resend-otp', [AuthController::class, 'resendOtp']);
Route::post('login', [AuthController::class, 'login']);
Route::post('menu', [AuthController::class, 'getMenuByRole']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/forgot-password/otp', [ForgotPasswordController::class, 'sendOtp']);
Route::post('/forgot-password/verify', [ForgotPasswordController::class, 'verifyOtp']);
Route::post('/forgot-password/reset', [ForgotPasswordController::class, 'resetPassword']);
Route::prefix('productsList')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('{id}', [ProductController::class, 'show']);
});
Route::middleware('auth:api')->group(function () {
    //Route::get('menu', [AuthController::class, 'getMenuByRole']);
    Route::get('menusRoles', [AuthController::class, 'listRolesWithMenus']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
    Route::post('/change-password', [UserController::class, 'changePassword']);


    Route::get('/menu-roles', [MenuRoleController::class, 'index']);
    Route::post('/menu-roles', [MenuRoleController::class, 'store']);
    Route::get('/menu-roles/{id}', [MenuRoleController::class, 'show']);
    Route::put('/menu-roles/{id}', [MenuRoleController::class, 'update']);
    Route::delete('/menu-roles/{id}', [MenuRoleController::class, 'destroy']);
    Route::get('/role', [MenuRoleController::class, 'indexRole']);

    Route::prefix('checkout')->group(function () {
        Route::post('/', [CheckoutController::class, 'checkout']);
    });

    Route::apiResource('pageinfoCrud', PageInfoController::class);

    // CRUD Kegiatan & Kajian. Category ditentukan dari URL, bukan dari payload.
    Route::post('/{category}', [ContentController::class, 'store'])
        ->where('category', 'kegiatan|kajian');
    Route::put('/{category}/{id}', [ContentController::class, 'update'])
        ->where('category', 'kegiatan|kajian')
        ->whereNumber('id');
    Route::patch('/{category}/{id}', [ContentController::class, 'update'])
        ->where('category', 'kegiatan|kajian')
        ->whereNumber('id');
    Route::delete('/{category}/{id}', [ContentController::class, 'destroy'])
        ->where('category', 'kegiatan|kajian')
        ->whereNumber('id');
    Route::post('/upload-image', [PageInfoController::class, 'upload']);
    Route::apiResource('menus', MenuController::class);
    Route::apiResource('barang', BarangController::class);
    Route::apiResource('pembeli', PembeliController::class);
    Route::apiResource('barang-masuk', BarangMasukController::class);
    Route::apiResource('detail-penjualan', DetailPenjualanController::class);
    Route::apiResource('penjualan', PenjualanController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('barang-harga', BarangHargaController::class);
    Route::apiResource('supplier', SupplierController::class);
    Route::get('get-harga', [BarangHargaController::class, 'getHarga']);
    Route::apiResource('barang-masuk-inv', BarangMasukInvController::class);
    Route::apiResource('roles', RoleController::class);
    Route::get('/roles/{id}/menus', [MenuRoleController::class, 'menus']);
    Route::post('/roles/{id}/menus', [MenuRoleController::class, 'saveMenus']);

    // Tambahan untuk ambil harga berdasarkan tanggal & barang_id
    Route::get('get-harga', [BarangHargaController::class, 'getHarga']);
    Route::prefix('transaksi')->group(function () {
        Route::post('/penjualan', [TransaksiController::class, 'penjualanStore']);
        Route::get('/rekap', [TransaksiController::class, 'rekap']);
    });
    Route::prefix('tabungan-qurban')->group(function () {
        Route::get('/', [TabunganQurbanController::class, 'index']);
        Route::get('{id}', [TabunganQurbanController::class, 'show']);
        Route::post('/', [TabunganQurbanController::class, 'store']);
        Route::put('{id}', [TabunganQurbanController::class, 'update']);
        Route::delete('{id}', [TabunganQurbanController::class, 'destroy']);
        Route::delete('/detail/{id}', [TabunganQurbanController::class, 'destroyDetail']);
        Route::get('/detail/{id}', [TabunganQurbanController::class, 'detail']);

        Route::post('setoran', [TabunganQurbanController::class, 'addSetoran']);
    });
    Route::prefix('products')->group(function () {
        Route::get('/paginate-by-user', [ProductController::class, 'paginateByUser']);
        Route::get('/', [ProductController::class, 'index']);
        Route::get('{id}', [ProductController::class, 'show'])->whereNumber('id');
        Route::post('/', [ProductController::class, 'store']);
        Route::put('{id}', [ProductController::class, 'update']);
        Route::delete('{id}', [ProductController::class, 'destroy']);
        Route::post('/upload-multiple', [ProductController::class, 'uploadMultiple']);
    });
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::get('/list', [CategoryController::class, 'list']); // dropdown
        Route::get('{id}', [CategoryController::class, 'show']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::put('{id}', [CategoryController::class, 'update']);
        Route::delete('{id}', [CategoryController::class, 'destroy']);
    });
    Route::prefix('shops')->group(function () {
        Route::get('/list', [ShopController::class, 'list']);
        Route::get('/', [ShopController::class, 'index']);
        Route::get('{id}', [ShopController::class, 'show']);
        Route::post('/', [ShopController::class, 'store']);
        Route::delete('{id}', [ShopController::class, 'destroy']);
        Route::put('{id}', [ShopController::class, 'update']);
    });
    Route::prefix('orders')->group(function () {
        Route::post('/', [OrderController::class, 'store']);
        Route::get('/my-orders', [OrderController::class, 'myOrders']);
        Route::get('/{id}', [OrderController::class, 'show']);
    });

    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);
    Route::delete('/cart-clear', [CartController::class, 'clear']);
    Route::put('/cart/{id}', [CartController::class, 'update']);

    Route::post('/checkout', [CheckoutController::class, 'checkout']);
});
