@extends('layouts.app') <!-- or your master layout -->

@section('content')
<div class="container mx-auto py-6">
    <h1 class="text-2xl font-bold mb-4">Upgrade Your Membership</h1>

    @if(session('info'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-4">
            {{ session('info') }}
        </div>
    @endif

    <form action="{{ route('upgrade.checkout') }}" method="POST" class="space-y-4">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($membershipPricing as $tier => $info)
                <label class="border rounded-lg p-4 cursor-pointer hover:shadow-md">
                    <input type="radio" name="tier" value="{{ $tier }}" required class="mr-2">
                    <strong class="text-xl">{{ ucfirst($tier) }}</strong> - 
                    <span>{{ $info['formatted_price'] }}</span>

                    <ul class="text-sm mt-2 list-disc pl-5 text-gray-700">
                        @foreach($info['benefits'] as $benefit)
                            <li>{{ $benefit }}</li>
                        @endforeach
                    </ul>
                </label>
            @endforeach
        </div>      
        
        <div class="mt-6">
            <label for="payment_method" class="block font-medium">Select Payment Method:</label>
            <select name="payment_method" id="payment_method" class="border p-2 rounded w-full" required>
                <option value="">-- Choose Payment Method --</option>
                <option value="card">Credit / Debit Card</option>
                <option value="gcash">GCash</option>
                <option value="grab_pay">GrabPay</option>
                <option value="paymaya">PayMaya</option>
            </select>
        </div>

        <div class="mt-6">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Proceed to Checkout
            </button>
        </div>

    </form>
</div>
@endsection
