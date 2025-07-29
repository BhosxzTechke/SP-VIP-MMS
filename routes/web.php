<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LandingController;

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

// Landing Page
Route::get('/', [LandingController::class, 'index'])->name('landing');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});





Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes
Route::middleware('auth')->group(function () {
    
    // Dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/free', [DashboardController::class, 'freeDashboard'])->name('dashboard.free');
    Route::get('/dashboard/vip', [DashboardController::class, 'vipDashboard'])->name('dashboard.vip');
    
    // Profile Routes
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
    
    // Referral Routes
    Route::get('/referrals', [DashboardController::class, 'referrals'])->name('referrals');
    Route::get('/referrals/history', [DashboardController::class, 'referralHistory'])->name('referrals.history');
    
    // Payment Routes
    Route::get('/upgrade', [PaymentController::class, 'showUpgrade'])->name('upgrade');
    Route::post('/upgrade/checkout', [PaymentController::class, 'checkout'])->name('upgrade.checkout');
    Route::get('/payment/success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
    Route::get('/payment/cancel', [PaymentController::class, 'paymentCancel'])->name('payment.cancel');
    
    // Transaction History
    Route::get('/transactions', [DashboardController::class, 'transactions'])->name('transactions');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // User Management
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
    Route::put('/users/{user}/membership', [AdminController::class, 'updateUserMembership'])->name('users.membership.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
    
    // Membership Management
    Route::get('/memberships', [AdminController::class, 'memberships'])->name('memberships');
    Route::put('/memberships/{membership}/status', [AdminController::class, 'updateMembershipStatus'])->name('memberships.status.update');
    
    // Referral Management
    Route::get('/referrals', [AdminController::class, 'referrals'])->name('referrals');
    Route::put('/referrals/{referral}/status', [AdminController::class, 'updateReferralStatus'])->name('referrals.status.update');
    Route::post('/referrals/bulk-payout', [AdminController::class, 'bulkPayout'])->name('referrals.bulk-payout');
    
    // Transaction Management
    Route::get('/transactions', [AdminController::class, 'transactions'])->name('transactions');
    Route::get('/transactions/{transaction}', [AdminController::class, 'showTransaction'])->name('transactions.show');
    
    // Reports
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::get('/reports/export', [AdminController::class, 'exportReports'])->name('reports.export');
    
    // Settings
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::put('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
});

// Public referral link
Route::get('/ref/{code}', [AuthController::class, 'referralLink'])->name('referral.link');
