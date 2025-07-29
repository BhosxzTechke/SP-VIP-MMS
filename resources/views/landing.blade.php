@extends('layouts.app')

@section('content')
<div class="bg-white">
    <!-- Hero Section -->
    <div class="relative overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
                <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                    <div class="sm:text-center lg:text-left">
                        <h1 class="text-4xl tracking-tight font-bold text-gray-900 sm:text-5xl md:text-6xl">
                            <span class="block xl:inline">Welcome to</span>
                            <span class="block text-primary-600 xl:inline">ShoPilipinas VIP</span>
                        </h1>
                        <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                            Join our exclusive VIP portal and unlock higher referral commissions. Start as a free member or upgrade to VIP for premium benefits and unlimited earning potential.
                        </p>
                        <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                            <div class="rounded-md shadow">
                                <a href="{{ route('register') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 md:py-4 md:text-lg md:px-10">
                                    Get Started Free
                                </a>
                            </div>
                            <div class="mt-3 sm:mt-0 sm:ml-3">
                                <a href="{{ route('login') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-primary-700 bg-primary-100 hover:bg-primary-200 md:py-4 md:text-lg md:px-10">
                                    Sign In
                                </a>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
        <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
            <div class="h-56 w-full bg-gradient-to-br from-primary-400 to-primary-600 sm:h-72 md:h-96 lg:w-full lg:h-full flex items-center justify-center">
                <div class="text-white text-center">
                    <div class="text-6xl font-bold mb-4">VIP</div>
                    <div class="text-xl">Exclusive Portal</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center">
                <h2 class="text-base text-primary-600 font-semibold tracking-wide uppercase">Membership Benefits</h2>
                <p class="mt-2 text-3xl leading-8 font-bold tracking-tight text-gray-900 sm:text-4xl">
                    Choose Your Path to Success
                </p>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">
                    Start free and earn commissions, or upgrade to VIP for higher rates and exclusive benefits.
                </p>
            </div>

            <div class="mt-10">
                <div class="space-y-10 md:space-y-0 md:grid md:grid-cols-2 md:gap-x-8 md:gap-y-10">
                    <!-- Free Membership -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <div class="text-center">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Free Membership</h3>
                            <div class="text-4xl font-bold text-gray-900 mb-6">₱0</div>
                        </div>
                        
                        <div class="space-y-4">
                            <h4 class="font-semibold text-gray-900">Commission Rates:</h4>
                            <ul class="space-y-2">
                                <li class="flex items-center">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                    <span>{{ $freeCommissionRates['gold'] }}% commission on Gold referrals</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                    <span>{{ $freeCommissionRates['platinum'] }}% commission on Platinum referrals</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="w-2 h-2 bg-red-500 rounded-full mr-3"></span>
                                    <span>Cannot refer Diamond tier</span>
                                </li>
                            </ul>
                            
                            <h4 class="font-semibold text-gray-900 pt-4">Benefits:</h4>
                            <ul class="space-y-2">
                                <li class="flex items-center">
                                    <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                                    <span>Unique referral code</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                                    <span>Basic customer support</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                                    <span>Commission tracking dashboard</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- VIP Membership -->
                    <div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg shadow-lg p-6 text-white">
                        <div class="text-center">
                            <h3 class="text-2xl font-bold mb-4">VIP Membership</h3>
                            <div class="text-sm opacity-90 mb-6">Starting from</div>
                        </div>
                        
                        <div class="space-y-6">
                            <!-- Gold Tier -->
                            <div class="bg-white bg-opacity-10 rounded-lg p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="font-bold text-gold-300">Gold VIP</h4>
                                    <span class="font-bold">{{ $membershipPricing['gold']['formatted_price'] }}</span>
                                </div>
                                <div class="text-sm opacity-90">
                                    {{ $membershipPricing['gold']['commission_rates']['gold'] }}% | {{ $membershipPricing['gold']['commission_rates']['platinum'] }}% | {{ $membershipPricing['gold']['commission_rates']['diamond'] }}% commission rates
                                </div>
                            </div>

                            <!-- Platinum Tier -->
                            <div class="bg-white bg-opacity-10 rounded-lg p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="font-bold text-gray-300">Platinum VIP</h4>
                                    <span class="font-bold">{{ $membershipPricing['platinum']['formatted_price'] }}</span>
                                </div>
                                <div class="text-sm opacity-90">
                                    {{ $membershipPricing['platinum']['commission_rates']['gold'] }}% | {{ $membershipPricing['platinum']['commission_rates']['platinum'] }}% | {{ $membershipPricing['platinum']['commission_rates']['diamond'] }}% commission rates
                                </div>
                            </div>

                            <!-- Diamond Tier -->
                            <div class="bg-white bg-opacity-10 rounded-lg p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="font-bold text-purple-300">Diamond VIP</h4>
                                    <span class="font-bold">{{ $membershipPricing['diamond']['formatted_price'] }}</span>
                                </div>
                                <div class="text-sm opacity-90">
                                    {{ $membershipPricing['diamond']['commission_rates']['gold'] }}% | {{ $membershipPricing['diamond']['commission_rates']['platinum'] }}% | {{ $membershipPricing['diamond']['commission_rates']['diamond'] }}% commission rates
                                </div>
                            </div>

                            <div class="pt-4">
                                <h4 class="font-semibold mb-2">VIP Benefits:</h4>
                                <ul class="space-y-1 text-sm">
                                    <li>• Can refer all membership tiers including Diamond</li>
                                    <li>• Higher commission rates on all referrals</li>
                                    <li>• Priority customer support</li>
                                    <li>• Exclusive VIP events and networking</li>
                                    <li>• Advanced analytics dashboard</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- How It Works Section -->
    <div class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center">
                <h2 class="text-base text-primary-600 font-semibold tracking-wide uppercase">How It Works</h2>
                <p class="mt-2 text-3xl leading-8 font-bold tracking-tight text-gray-900 sm:text-4xl">
                    Simple Steps to Start Earning
                </p>
            </div>

            <div class="mt-10">
                <div class="space-y-10 md:space-y-0 md:grid md:grid-cols-3 md:gap-x-8 md:gap-y-10">
                    <div class="text-center">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-primary-500 text-white mx-auto text-xl font-bold">
                            1
                        </div>
                        <h3 class="mt-6 text-lg leading-6 font-medium text-gray-900">Register for Free</h3>
                        <p class="mt-2 text-base text-gray-500">
                            Create your free account and get your unique referral code instantly.
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-primary-500 text-white mx-auto text-xl font-bold">
                            2
                        </div>
                        <h3 class="mt-6 text-lg leading-6 font-medium text-gray-900">Refer Friends</h3>
                        <p class="mt-2 text-base text-gray-500">
                            Share your referral code with friends and family. Earn commissions when they upgrade.
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-primary-500 text-white mx-auto text-xl font-bold">
                            3
                        </div>
                        <h3 class="mt-6 text-lg leading-6 font-medium text-gray-900">Upgrade for More</h3>
                        <p class="mt-2 text-base text-gray-500">
                            Upgrade to VIP for higher commission rates and the ability to refer Diamond tier.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-primary-600">
        <div class="max-w-2xl mx-auto text-center py-16 px-4 sm:py-20 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-white sm:text-4xl">
                <span class="block">Ready to start earning?</span>
                <span class="block">Join ShoPilipinas VIP today.</span>
            </h2>
            <p class="mt-4 text-lg leading-6 text-primary-200">
                Start with a free account and begin referring friends immediately. Upgrade anytime to unlock higher commissions.
            </p>
            <a href="{{ route('register') }}" class="mt-8 w-full inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-primary-600 bg-white hover:bg-primary-50 sm:w-auto">
                Get Started Now
            </a>
        </div>
    </div>
</div>
@endsection
