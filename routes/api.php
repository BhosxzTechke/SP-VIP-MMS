<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// PayMongo Webhook Routes
Route::post('/webhook/paymongo', [PaymentController::class, 'webhook'])->name('webhook.paymongo');

// Public API Routes for referral validation
Route::get('/referral/validate/{code}', function ($code) {
    $user = \App\Models\User::where('referral_code', $code)->first();
    
    if (!$user) {
        return response()->json([
            'valid' => false,
            'message' => 'Invalid referral code'
        ], 404);
    }
    
    return response()->json([
        'valid' => true,
        'referrer' => [
            'name' => $user->name,
            'membership_type' => $user->membership_type,
            'can_refer_diamond' => $user->canReferDiamond(),
        ]
    ]);
})->name('api.referral.validate');

// Membership pricing API
Route::get('/membership/pricing', function () {
    return response()->json([
        'pricing' => [
            'gold' => [
                'amount' => config('paymongo.membership_prices.gold'),
                'amount_formatted' => '₱' . number_format(config('paymongo.membership_prices.gold') / 100, 2),
                'benefits' => [
                    '5% commission on Gold referrals',
                    '8% commission on Platinum referrals', 
                    '12% commission on Diamond referrals',
                    'Can refer all membership tiers',
                    'Priority customer support'
                ]
            ],
            'platinum' => [
                'amount' => config('paymongo.membership_prices.platinum'),
                'amount_formatted' => '₱' . number_format(config('paymongo.membership_prices.platinum') / 100, 2),
                'benefits' => [
                    '5% commission on Gold referrals',
                    '8% commission on Platinum referrals',
                    '12% commission on Diamond referrals',
                    'Can refer all membership tiers',
                    'Priority customer support',
                    'Exclusive Platinum member events'
                ]
            ],
            'diamond' => [
                'amount' => config('paymongo.membership_prices.diamond'),
                'amount_formatted' => '₱' . number_format(config('paymongo.membership_prices.diamond') / 100, 2),
                'benefits' => [
                    '5% commission on Gold referrals',
                    '8% commission on Platinum referrals',
                    '12% commission on Diamond referrals',
                    'Can refer all membership tiers',
                    'Priority customer support',
                    'Exclusive Diamond member events',
                    'Personal account manager',
                    'Highest commission rates'
                ]
            ]
        ],
        'free_benefits' => [
            '3% commission on Gold referrals',
            '5% commission on Platinum referrals',
            'Cannot refer Diamond tier',
            'Basic customer support'
        ]
    ]);
})->name('api.membership.pricing');
