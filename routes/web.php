<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockOutController;

use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BarcodeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Http\Request;
use App\Models\Product;

Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);

// Redirect root to login
Route::get('/', function () {
    return redirect('/login');
});

// ✅ PINDAHKAN API BARCODE KE SINI - TIDAK PERLU LOGIN
Route::get('/api/products/search-by-barcode', function (Request $request) {
    $barcode = $request->get('barcode');
    $product = Product::where('barcode', $barcode)->first();
    
    if($product) {
        return response()->json([
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'selling_price' => $product->selling_price,
                'stock' => $product->stock,
                'barcode' => $product->barcode,
                'code' => $product->code
            ]
        ]);
    }
    
    return response()->json(['product' => null]);
})->name('api.products.search-by-barcode');

// Authentication Routes
Route::middleware('guest')->group(function () {
    // Login Routes
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);
    
    // Password Reset Routes
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Email Verification
    Route::get('verify-email', [EmailVerificationNotificationController::class, '__invoke'])
        ->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Logout
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // API Routes untuk mobile/app
    Route::prefix('api')->group(function () {
        Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.api.show');
        Route::get('/products/category/{category}', [ProductController::class, 'getByCategory'])->name('products.by-category');
        Route::get('/products-search', [ProductController::class, 'search'])->name('products.search.api');
        Route::get('/products-low-stock', [ProductController::class, 'apiLowStock'])->name('api.products.low-stock');
    });
});

// Products Routes (Hanya Admin dan Gudang)
Route::middleware(['auth', 'gudang'])->prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('products.index');
    Route::get('/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/', [ProductController::class, 'store'])->name('products.store');
    Route::get('/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    
    // Additional Product Routes
    Route::get('/generate/code', [ProductController::class, 'generateCode'])->name('products.generate-code');
    Route::get('/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::get('/low-stock', [ProductController::class, 'lowStock'])->name('products.low-stock');
    Route::post('/bulk-action', [ProductController::class, 'bulkAction'])->name('products.bulk-action');
    Route::get('/search', [ProductController::class, 'search'])->name('products.search');
    Route::post('/{product}/quick-update-stock', [ProductController::class, 'quickUpdateStock'])->name('products.quick-update-stock');
    
    // Barcode Routes
    Route::get('/{product}/barcode', [BarcodeController::class, 'generateBarcode'])->name('products.barcode');
    Route::get('/{product}/download-barcode', [BarcodeController::class, 'downloadBarcode'])->name('products.download-barcode');
});

// Categories Routes (Hanya Admin dan Gudang)
Route::middleware(['auth', 'gudang'])->prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});

// Stock In Routes (Hanya Admin dan Gudang)
Route::middleware(['auth', 'gudang'])->prefix('stock-ins')->group(function () {
    Route::get('/', [StockInController::class, 'index'])->name('stock-ins.index');
    Route::get('/create', [StockInController::class, 'create'])->name('stock-ins.create');
    Route::post('/', [StockInController::class, 'store'])->name('stock-ins.store');
    Route::get('/{stockIn}', [StockInController::class, 'show'])->name('stock-ins.show');
    Route::get('/{stockIn}/edit', [StockInController::class, 'edit'])->name('stock-ins.edit');
    Route::put('/{stockIn}', [StockInController::class, 'update'])->name('stock-ins.update');
    Route::delete('/{stockIn}', [StockInController::class, 'destroy'])->name('stock-ins.destroy');
    Route::get('/generate/invoice', [StockInController::class, 'generateInvoice'])->name('stock-ins.generate-invoice');
});

// Stock Out Routes (Hanya Admin dan Kasir)
Route::middleware(['auth', 'kasir'])->prefix('stock-outs')->group(function () {
    Route::get('/', [StockOutController::class, 'index'])->name('stock-outs.index');
    Route::get('/create', [StockOutController::class, 'create'])->name('stock-outs.create');
    Route::post('/', [StockOutController::class, 'store'])->name('stock-outs.store');
    Route::get('/{stockOut}', [StockOutController::class, 'show'])->name('stock-outs.show');
    Route::get('/{stockOut}/edit', [StockOutController::class, 'edit'])->name('stock-outs.edit');
    Route::put('/{stockOut}', [StockOutController::class, 'update'])->name('stock-outs.update');
    Route::delete('/{stockOut}', [StockOutController::class, 'destroy'])->name('stock-outs.destroy');
    Route::get('/generate/invoice', [StockOutController::class, 'generateInvoice'])->name('stock-outs.generate-invoice');
});

// Reports Routes (Hanya Admin)
Route::middleware(['auth', 'admin'])->prefix('reports')->group(function () {
    Route::get('/stock', [ReportController::class, 'stockReport'])->name('reports.stock');
    Route::get('/movement', [ReportController::class, 'stockMovement'])->name('reports.movement');
    Route::get('/income', [ReportController::class, 'incomeReport'])->name('reports.income');
});



// Fallback Route
Route::fallback(function () {
    return redirect('/dashboard');
});