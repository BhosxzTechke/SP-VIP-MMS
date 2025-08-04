
@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between">



            {{-- <div class="flex-1 min-w-0">
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
            </div> --}}






        </div>


      



        <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
            <!-- Referral Code Section -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-4 sm:p-6">
  


                <div class="flex items-center space-x-4">
                <img src="upload/image.jpg" class="w-12 h-12 rounded-full" alt="Profile Picture" />
                <div>
                    <strong class="block">{{ $user->name }}</strong>
                    <span class="text-sm text-gray-600">{{ $user->membership_display }}</span>
                </div>
                </div>


<br>
                   <div class="mt-2 max-w-xl text-sm text-gray-500">
                       <strong class="block">Email</strong> <p>{{ $user->email }}</p>
                    </div>


                      <div class="mt-2 max-w-xl text-sm text-gray-500">
                        <strong class="block">Refferal Code</strong> <p>{{ $user->referral_code }}</p>
                    </div>


                      <div class="mt-2 max-w-xl text-sm text-gray-500">
                        <strong class="block">Phone</strong> <p>{{ $user->phone }}</p>
                    </div>

      

                    
                </div>
            </div>


            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

            <!-- Commission Rates -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Profile Page</h3>
                    <div class="mt-2 max-w-xl text-sm text-gray-500">
                        <p>Enter your Name</p>
                    </div>



                      <div class="flex items-center space-x-3">
                            <div class="flex-1">
                    <input type="text" name="name" value="{{ $user->name }}">
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-900 text-lg font-mono">
                            </div>

                        </div>


                     <div class="mt-2 max-w-xl text-sm text-gray-500">
                        <p>Enter your Email</p>
                    </div>

                       <div class="flex items-center space-x-3">
                            <div class="flex-1">
                                <input type="text" required=""
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-900 text-lg font-mono">
                            </div>

                        </div>

                      <div class="mt-2 max-w-xl text-sm text-gray-500">
                        <p>Enter your Phone</p>
                    </div>

                       <div class="flex items-center space-x-3">
                            <div class="flex-1">
                                <input type="text" required=""
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-900 text-lg font-mono">
                            </div>

                        </div>



                     <div class="mt-2 max-w-xl text-sm text-gray-500">
                        <p>Enter your Current Password</p>
                    </div>

                       <div class="flex items-center space-x-3">
                            <div class="flex-1">
                                <input type="text" required=""
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-900 text-lg font-mono">
                            </div>

                        </div>


                   <div class="mt-2 max-w-xl text-sm text-gray-500">
                        <p>Enter your Password</p>
                    </div>

                       <div class="flex items-center space-x-3">
                            <div class="flex-1">
                                <input type="text" required="" 
                                       class="block w-full px-3 py-2  border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-900 text-lg font-mono">
                            </div>

                        </div>

                        <br>


                        
                <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                            Save Changes
                </button>


                </div>
            </div>

</form> 


        </div>

        
        {{-- @endif --}}

        {{-- <!-- Upgrade CTA -->
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
                            <p class="text-sm opacity-90">{{ $pricing['commission_rates'][0] ?? 'N/A' }}% commission</p>
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
</div> --}}

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
