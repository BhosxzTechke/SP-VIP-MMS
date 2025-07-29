<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Display the landing page.
     */
    public function index()
    {
        $membershipPricing = [
            'gold' => [
                'price' => config('paymongo.membership_prices.gold') / 100,
                'formatted_price' => '₱' . number_format(config('paymongo.membership_prices.gold') / 100, 0),
                'commission_rates' => config('paymongo.commission_rates.vip'),
            ],
            'platinum' => [
                'price' => config('paymongo.membership_prices.platinum') / 100,
                'formatted_price' => '₱' . number_format(config('paymongo.membership_prices.platinum') / 100, 0),
                'commission_rates' => config('paymongo.commission_rates.vip'),
            ],
            'diamond' => [
                'price' => config('paymongo.membership_prices.diamond') / 100,
                'formatted_price' => '₱' . number_format(config('paymongo.membership_prices.diamond') / 100, 0),
                'commission_rates' => config('paymongo.commission_rates.vip'),
            ],
        ];

        $freeCommissionRates = config('paymongo.commission_rates.free');

        return view('landing', compact('membershipPricing', 'freeCommissionRates'));
    }
}
