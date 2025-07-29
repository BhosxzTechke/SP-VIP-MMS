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
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            @if($user->membership_type === 'gold') bg-yellow-100 text-yellow-800
                            @elseif($user->membership_type === 'platinum') bg-gray-100 text-gray-800
                            @elseif($user->membership_type === 'diamond') bg-purple-100 text-purple-800
                            @endif">
                            {{ $user->membership_display }}
                        </span>
                    </div>
                    @if($user->membership_upgraded_at)
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                        <span>Upgraded {{ $user->membership_upgraded_at->format('M d, Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="mt-8">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5">
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
                                    <span class="text-white text-sm font-bold">{{ $stats['gold_referrals'] }}</span>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Gold</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['gold_referrals'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-gray-500 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-bold">{{ $stats['platinum_referrals'] }}</span>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Platinum</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['platinum_referrals'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-bold">{{ $stats['diamond_referrals'] }}</span>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Diamond</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['diamond_referrals'] }}</dd>
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
                                    <dt class="text-sm font-medium text-gray-500 truncate">This Month</dt>
                                    <dd class="text-lg font-medium text-gray-900">₱{{ number_format($stats['this_month_commission'], 2) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Commission Overview -->
        <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Commission Overview</h3>
                    <div class="mt-5 grid grid-cols-2 gap-4">
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">₱{{ number_format($stats['total_commission'], 2) }}</div>
                            <div class="text-sm text-green-800">Total Earned</div>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-yellow-600">₱{{ number_format($stats['pending_commission'], 2) }}</div>
                            <div class="text-sm text-yellow-800">Pending</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- VIP Referral Code -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Your VIP Referral Code</h3>
                    <div class="mt-2 max-w-xl text-sm text-gray-500">
                        <p>Share this VIP code to refer all membership tiers including Diamond.</p>
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
                                VIP Referral Link: 
                                <span class="font-mono text-primary-600">{{ url('/ref/' . $user->referral_code) }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Commission Rates -->
        <div class="mt-8">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Your VIP Commission Rates</h3>
                    <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div class="bg-yellow-50 p-4 rounded-lg border-l-4 border-yellow-400">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="font-medium text-yellow-800">Gold VIP Referral</h4>
                                    <p class="text-sm text-yellow-600">₱3,000 membership</p>
                                </div>
                                <div class="text-2xl font-bold text-yellow-600">5%</div>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-gray-400">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="font-medium text-gray-800">Platinum VIP Referral</h4>
                                    <p class="text-sm text-gray-600">₱30,000 membership</p>
                                </div>
                                <div class="text-2xl font-bold text-gray-600">8%</div>
                            </div>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-400">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="font-medium text-purple-800">Diamond VIP Referral</h4>
                                    <p class="text-sm text-purple-600">₱300,000 membership</p>
                                </div>
                                <div class="text-2xl font-bold text-purple-600">12%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Commission Chart -->
        @if($monthlyCommissions->count() > 0)
        <div class="mt-8">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Monthly Commission History</h3>
                    <div class="space-y-3">
                        @foreach($monthlyCommissions as $commission)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="font-medium text-gray-900">
                                {{ DateTime::createFromFormat('!m', $commission->month)->format('F') }} {{ $commission->year }}
                            </span>
                            <span class="text-lg font-bold text-green-600">₱{{ number_format($commission->total, 2) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif

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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rate</th>
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
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($referral->membership_tier_referred === 'gold') bg-yellow-100 text-yellow-800
                                            @elseif($referral->membership_tier_referred === 'platinum') bg-gray-100 text-gray-800
                                            @elseif($referral->membership_tier_referred === 'diamond') bg-purple-100 text-purple-800
                                            @endif">
                                            {{ $referral->tier_display }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $referral->formatted_commission }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $referral->commission_rate }}%
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

        <!-- VIP Benefits -->
        <div class="mt-8">
            <div class="bg-gradient-to-r from-primary-500 to-primary-600 rounded-lg shadow-lg">
                <div class="px-6 py-8 sm:px-10 sm:py-10">
                    <div class="max-w-3xl mx-auto text-center">
                        <h2 class="text-3xl font-bold text-white">You're a VIP Member!</h2>
                        <p class="mt-4 text-lg text-primary-100">
                            Enjoy exclusive benefits and higher commission rates on all referrals.
                        </p>
                        <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <div class="bg-white bg-opacity-10 rounded-lg p-4 text-white">
                                <div class="text-2xl font-bold">5-12%</div>
                                <div class="text-sm opacity-90">Commission Rates</div>
                            </div>
                            <div class="bg-white bg-opacity-10 rounded-lg p-4 text-white">
                                <div class="text-2xl font-bold">All Tiers</div>
                                <div class="text-sm opacity-90">Referral Access</div>
                            </div>
                            <div class="bg-white bg-opacity-10 rounded-lg p-4 text-white">
                                <div class="text-2xl font-bold">Priority</div>
                                <div class="text-sm opacity-90">Customer Support</div>
                            </div>
                            <div class="bg-white bg-opacity-10 rounded-lg p-4 text-white">
                                <div class="text-2xl font-bold">Exclusive</div>
                                <div class="text-sm opacity-90">VIP Events</div>
                            </div>
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
