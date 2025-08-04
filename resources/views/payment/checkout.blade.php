@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-10 px-6 bg-white shadow-lg rounded-lg">
    <h1 class="text-3xl font-bold text-center mb-6 text-blue-700">VIP Membership Checkout</h1>

    <div class="border p-4 rounded mb-6">
        <h2 class="text-xl font-semibold mb-2 text-gray-800">Membership Details</h2>
        <ul class="text-sm text-gray-700 space-y-1">
            <li><strong>Tier:</strong> <span class="capitalize">{{ $tier }}</span></li>
            <li><strong>Amount:</strong> {{ $formattedAmount }}</li>
            <li><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $paymentMethod)) }}</li>
        </ul>
    </div>

    <div class="bg-yellow-50 p-4 rounded border-l-4 border-yellow-400 mb-6">
        <h3 class="font-semibold text-yellow-700">Test Mode Instructions</h3>
        <p class="text-sm text-yellow-700 mt-2">
            You are currently in <strong>PayMongo Test Mode</strong>. Use the test payment method to simulate a successful transaction.
        </p>
        <ol class="list-decimal ml-6 mt-3 space-y-1 text-sm text-yellow-800">
            <li>Use card number <code class="bg-gray-200 px-1 py-0.5 rounded">4242 4242 4242 4242</code> with any future expiry and CVC <code>123</code>.</li>
            <li>Use Postman or your backend to attach the payment method to this intent:</li>
        </ol>
        <div class="bg-white border border-gray-200 p-3 mt-3 rounded">
            <p class="text-sm"><strong>Payment Intent ID:</strong> <code class="text-blue-600">{{ $paymentIntent['id'] }}</code></p>
            <p class="text-sm"><strong>Client Key:</strong> <code class="text-blue-600">{{ $clientKey }}</code></p>
        </div>
    </div>

    <div class="flex justify-center mt-6">
        <a href="{{ route('dashboard') }}"
           class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition duration-150">
            Return to Dashboard
        </a>
    </div>
</div>
@endsection
