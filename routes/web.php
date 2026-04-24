<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\POSController;
use App\Http\Controllers\ReconciliationController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServicePackageController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\FbrController;
use App\Http\Controllers\CategoryController;

Route::get('/', fn() => redirect()->route('pos.index'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');

    // Password Reset
    Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

Route::middleware('web')->group(function () {
    Route::get('/session-expired', [LoginController::class, 'sessionExpired'])->name('session.expired');
});

Route::middleware(['web', 'auth', 'session.active'])->group(function () {
    Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
    Route::get('/pos/payment', [POSController::class, 'payment'])->name('pos.payment');
    Route::get('/pos/check-coupon', [POSController::class, 'checkCoupon'])->name('pos.check-coupon');
    Route::post('/pos/checkout', [POSController::class, 'store'])->name('pos.store');

    // Service Management
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
    Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');

    // Package Management
    Route::get('/packages', [ServicePackageController::class, 'index'])->name('packages.index');
    Route::get('/packages/create', [ServicePackageController::class, 'create'])->name('packages.create');
    Route::post('/packages', [ServicePackageController::class, 'store'])->name('packages.store');
    Route::get('/packages/{package}/edit', [ServicePackageController::class, 'edit'])->name('packages.edit');
    Route::put('/packages/{package}', [ServicePackageController::class, 'update'])->name('packages.update');
    Route::delete('/packages/{package}', [ServicePackageController::class, 'destroy'])->name('packages.destroy');
    // Invoice Management
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/export', [InvoiceController::class, 'export'])->name('invoices.export');
    Route::get('/invoices/{invoice}/ticket', [InvoiceController::class, 'ticket'])->name('invoices.ticket');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/sales-history/{invoice}', [InvoiceController::class, 'historyShow'])->name('sales-history.show');

    // FBR Integration Testing
    Route::get('/fbr-integration', [FbrController::class, 'index'])->name('fbr.index');
    Route::get('/fbr-integration/invoice/{invoice}', [FbrController::class, 'show'])->name('fbr.show');

    // Category Management
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Inventory Management
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.dashboard');

    // Customer Management
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');

    // Staff Management
    Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');

    // Reports Management
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Admin Dashboard
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.index');

    // Purchases
    Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// Port test for FBR integration (Public for debugging)
Route::get('/test-fbr-port', function () {
    $host = 'esp.fbr.gov.pk';
    $port = 8244;

    $connection = @fsockopen($host, $port, $errno, $errstr, 5);

    if ($connection) {
        fclose($connection);
        return "✅ Port 8244 is OPEN - FBR connection works!";
    } else {
        return "❌ Port 8244 is BLOCKED - Error: $errstr (Code: $errno)";
    }
});
