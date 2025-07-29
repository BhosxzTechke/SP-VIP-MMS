@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Admin Dashboard
                </h2>
                <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                        <span>ShoPilipinas VIP Portal Management</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="mt-8">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-bold">{{ $stats['total_users'] }}</span>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_users']) }}</dd>
                                </dl>
                            </div>
                        </div>
                        <div class="mt-3 text-sm text-gray-500">
                            <span class="text-green-600">{{ $stats['free_users'] }}</span> free, 
                            <span class="text-primary-600">{{ $stats['vip_users'] }}</span> VIP
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
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Revenue</dt>
                                    <dd class="text-lg font-medium text-gray-900">₱{{ number_format($stats['total_revenue'], 2) }}</dd>
                                </dl>
                            </div>
                        </div>
                        <div class="mt-3 text-sm text-gray-500">
                            <span class="text-yellow-600">{{ $stats['pending_payments'] }}</span> pending payments
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-bold">{{ $stats['total_referrals'] }}</span>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Referrals</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_referrals']) }}</dd>
                                </dl>
                            </div>
                        </div>
                        <div class="mt-3 text-sm text-gray-500">
                            <span class="text-green-600">₱{{ number_format($stats['paid_commissions'], 2) }}</span> paid out
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
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pending Commissions</dt>
                                    <dd class="text-lg font-medium text-gray-900">₱{{ number_format($stats['pending_commissions'], 2) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- VIP Membership Breakdown -->
        <div class="mt-8">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">VIP Membership Breakdown</h3>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div class="bg-yellow-50 p-4 rounded-lg border-l-4 border-yellow-400">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="font-medium text-yellow-800">Gold Members</h4>
                                    <p class="text-sm text-yellow-600">₱3,000 tier</p>
                                </div>
                                <div class="text-2xl font-bold text-yellow-600">{{ $stats['gold_members'] }}</div>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-gray-400">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="font-medium text-gray-800">Platinum Members</h4>
                                    <p class="text-sm text-gray-600">₱30,000 tier</p>
                                </div>
                                <div class="text-2xl font-bold text-gray-600">{{ $stats['platinum_members'] }}</div>
                            </div>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-400">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="font-medium text-purple-800">Diamond Members</h4>
                                    <p class="text-sm text-purple-600">₱300,000 tier</p>
                                </div>
                                <div class="text-2xl font-bold text-purple-600">{{ $stats['diamond_members'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <a href="{{ route('admin.users') }}" class="bg-blue-50 hover:bg-blue-100 p-4 rounded-lg border border-blue-200 transition-colors">
                            <div class="text-blue-600 font-medium">Manage Users</div>
                            <div class="text-sm text-blue-500 mt-1">View and manage all users</div>
                        </a>
                        <a href="{{ route('admin.memberships') }}" class="bg-green-50 hover:bg-green-100 p-4 rounded-lg border border-green-200 transition-colors">
                            <div class="text-green-600 font-medium">Memberships</div>
                            <div class="text-sm text-green-500 mt-1">Review membership payments</div>
                        </a>
                        <a href="{{ route('admin.referrals') }}" class="bg-purple-50 hover:bg-purple-100 p-4 rounded-lg border border-purple-200 transition-colors">
                            <div class="text-purple-600 font-medium">Referrals</div>
                            <div class="text-sm text-purple-500 mt-1">Manage referral commissions</div>
                        </a>
                        <a href="{{ route('admin.reports') }}" class="bg-orange-50 hover:bg-orange-100 p-4 rounded-lg border border-orange-200 transition-colors">
                            <div class="text-orange-600 font-medium">Reports</div>
                            <div class="text-sm text-orange-500 mt-1">View business analytics</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
            <!-- Recent Users -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recent Users</h3>
                    <div class="space-y-3">
                        @foreach($recentUsers as $user)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($user->membership_type === 'free') bg-gray-100 text-gray-800
                                    @elseif($user->membership_type === 'gold') bg-yellow-100 text-yellow-800
                                    @elseif($user->membership_type === 'platinum') bg-gray-100 text-gray-800
                                    @elseif($user->membership_type === 'diamond') bg-purple-100 text-purple-800
                                    @endif">
                                    {{ $user->membership_display }}
                                </span>
                                <div class="text-xs text-gray-500 mt-1">{{ $user->created_at->format('M d, Y') }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('admin.users') }}" class="text-primary-600 hover:text-primary-500 text-sm font-medium">
                            View all users →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Memberships -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recent Memberships</h3>
                    <div class="space-y-3">
                        @foreach($recentMemberships as $membership)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <div class="font-medium text-gray-900">{{ $membership->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $membership->tier_display }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-medium text-gray-900">{{ $membership->formatted_amount }}</div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($membership->payment_status === 'completed') bg-green-100 text-green-800
                                    @elseif($membership->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($membership->payment_status) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('admin.memberships') }}" class="text-primary-600 hover:text-primary-500 text-sm font-medium">
                            View all memberships →
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue Chart -->
        @if($monthlyRevenue->count() > 0)
        <div class="mt-8">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Monthly Revenue Trend</h3>
                    <div class="space-y-3">
                        @foreach($monthlyRevenue as $revenue)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="font-medium text-gray-900">
                                {{ DateTime::createFromFormat('!m', $revenue->month)->format('F') }} {{ $revenue->year }}
                            </span>
                            <span class="text-lg font-bold text-green-600">₱{{ number_format($revenue->total, 2) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Recent Transactions -->
        <div class="mt-8">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recent Transactions</h3>
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recentTransactions as $transaction)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $transaction->user->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $transaction->type_display }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $transaction->formatted_amount }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($transaction->payment_status === 'completed') bg-green-100 text-green-800
                                            @elseif($transaction->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($transaction->payment_status === 'processing') bg-blue-100 text-blue-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ $transaction->status_display }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $transaction->created_at->format('M d, Y') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('admin.transactions') }}" class="text-primary-600 hover:text-primary-500 text-sm font-medium">
                            View all transactions →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
