@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-gray-100 p-6">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Choose Your VIP Membership</h1>

        <form method="POST" action="{{ route('upgrade.checkout') }}" class="space-y-8">
            @csrf

            @foreach ($membershipPricing as $tier => $info)
                <div class="bg-white shadow-md rounded p-6">
                    <label class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-semibold capitalize">{{ $tier }} Membership</h2>
                            <p class="text-gray-600">{{ $info['formatted_price'] }}</p>
                            <ul class="mt-2 text-sm text-gray-500 list-disc pl-5">
                                @foreach ($info['benefits'] as $benefit)
                                    <li>{{ $benefit }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <input type="radio" name="tier" value="{{ $tier }}" class="h-5 w-5 text-blue-600" required>
                    </label>
                </div>
            @endforeach

            <div>
                <label for="payment_method" class="block font-medium mb-2">Select Payment Method:</label>
                <select name="payment_method" class="w-full p-2 border border-gray-300 rounded" required>
                    <option value="">-- Choose One --</option>
                    <option value="gcash">GCash</option>
                    <option value="card">Card</option>
                    <option value="grab_pay">GrabPay</option>
                    <option value="paymaya">PayMaya</option>
                </select>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                Proceed to Payment
            </button>
        </form>
    </div>
</div>

@endsection