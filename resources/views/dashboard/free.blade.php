@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Welcome back, {{ $user->name }}!
                </h2>
                <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            {{ $user->membership_display }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                <a href="{{ route('upgrade') }}" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Upgrade to VIP
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="mt-8">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-bold">{{ $stats['total_referrals'] }}</span>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Referrals</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['total_referrals'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-bold">{{ $stats['pending_referrals'] }}</span>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['pending_referrals'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm">₱</span>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Earned</dt>
                                    <dd class="text-lg font-medium text-gray-900">₱{{ number_format($stats['total_commission'], 2) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm">₱</span>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pending Commission</dt>
                                    <dd class="text-lg font-medium text-gray-900">₱{{ number_format($stats['pending_commission'], 2) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
            <!-- Referral Code Section -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Your Referral Code</h3>
                    <div class="mt-2 max-w-xl text-sm text-gray-500">
                        <p>Share this code with friends to earn commissions when they upgrade to VIP.</p>
                    </div>
                    <div class="mt-5">
                        <div class="flex items-center space-x-3">
                            <div class="flex-1">
                                <input type="text" readonly value="{{ $user->referral_code }}" 
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-900 text-lg font-mono">
                            </div>
                            <button onclick="copyReferralCode()" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                                Copy
                            </button>
                        </div>
                        <div class="mt-3">
                            <p class="text-sm text-gray-500">
                                Referral Link: 
                                <span class="font-mono text-primary-600">{{ url('/ref/' . $user->referral_code) }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Commission Rates -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Your Commission Rates</h3>
                    <div class="mt-2 max-w-xl text-sm text-gray-500">
                        <p>Earn commissions when your referrals upgrade to VIP memberships.</p>
                    </div>
                    <div class="mt-5 space-y-3">
                        <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg">
                            <span class="font-medium text-yellow-800">Gold VIP Referral</span>
                            <span class="text-yellow-600 font-bold">3%</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="font-medium text-gray-800">Platinum VIP Referral</span>
                            <span class="text-gray-600 font-bold">5%</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                            <span class="font-medium text-red-800">Diamond VIP Referral</span>
                            <span class="text-red-600 font-bold">Not Available</span>
                        </div>
                    </div>
                    <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                        <p class="text-sm text-blue-800">
                            <strong>Upgrade to VIP</strong> to unlock higher commission rates and refer Diamond tier members!
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Referrals -->
        @if($recentReferrals->count() > 0)
        <div class="mt-8">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recent Referrals</h3>
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referred User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tier</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commission</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recentReferrals as $referral)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $referral->referred->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $referral->tier_display }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $referral->formatted_commission }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($referral->status === 'paid') bg-green-100 text-green-800
                                            @elseif($referral->status === 'approved') bg-blue-100 text-blue-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ $referral->status_display }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $referral->created_at->format('M d, Y') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('referrals') }}" class="text-primary-600 hover:text-primary-500 text-sm font-medium">
                            View all referrals →
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Upgrade CTA -->
        <div class="mt-8">
            <div class="bg-gradient-to-r from-primary-500 to-primary-600 rounded-lg shadow-lg">
                <div class="px-6 py-8 sm:px-10 sm:py-10">
                    <div class="max-w-3xl mx-auto text-center">
                        <h2 class="text-3xl font-bold text-white">Ready to Unlock Higher Commissions?</h2>
                        <p class="mt-4 text-lg text-primary-100">
                            Upgrade to VIP and earn up to 12% commission on all referrals, including Diamond tier members.
                        </p>
                        <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
                            @foreach($membershipPricing as $tier => $pricing)
                            <div class="bg-white bg-opacity-10 rounded-lg p-4 text-white">
                                <h3 class="font-bold text-lg capitalize">{{ $tier }} VIP</h3>
                                <p class="text-2xl font-bold">{{ $pricing['formatted_price'] }}</p>
                                <p class="text-sm opacity-90">Up to {{ max($pricing['commission_rates']) }}% commission</p>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-8">
                            <a href="{{ route('upgrade') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-primary-600 bg-white hover:bg-gray-50">
                                Upgrade Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyReferralCode() {
    const input = document.querySelector('input[value="{{ $user->referral_code }}"]');
    input.select();
    document.execCommand('copy');
    
    // Show feedback
    const button = event.target;
    const originalText = button.textContent;
    button.textContent = 'Copied!';
    button.classList.add('bg-green-600');
    button.classList.remove('bg-primary-600');
    
    setTimeout(() => {
        button.textContent = originalText;
        button.classList.remove('bg-green-600');
        button.classList.add('bg-primary-600');
    }, 2000);
}
</script>
@endsection
