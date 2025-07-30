<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Referral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log;



class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        return view('auth.login');
    }




    

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
             // Redirect based on user type
            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));

            } elseif ($user->membership_type === 'free') {
                return redirect()->route('dashboard.free');


            } elseif ($user->membership_type === 'vip') {
                return redirect()->route('dashboard.vip');

         }


        
            return redirect()->intended(route('dashboard'));
        }



        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput();
    }

    /**
     * Show the registration form.
     */
    public function showRegister(Request $request)
    {
        $referralCode = $request->get('ref');
        $referrer = null;

        if ($referralCode) {
            $referrer = User::where('referral_code', $referralCode)->first();
            if (!$referrer) {
                session()->flash('error', 'Invalid referral code provided.');
            }
        }

        return view('auth.register', compact('referralCode', 'referrer'));
    }



    

    /**
     * Handle registration request.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Password::min(8)],
            'referral_code' => 'nullable|string|exists:users,referral_code',
            'terms' => 'required|accepted',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Create the user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'membership_type' => 'free',
            ]);

            // Handle referral if provided
            if ($request->referral_code) {
                $referrer = User::where('referral_code', $request->referral_code)->first();
                
                if ($referrer) {
                    Referral::create([
                        'referrer_id' => $referrer->id,
                        'referred_id' => $user->id,
                        'status' => 'pending',
                    ]);
                }
            }


            // Log the user in
            Auth::login($user);

            return redirect()->route('dashboard')->with('success', 'Registration successful! Welcome to ShoPilipinas VIP Portal.');

            } catch (\Exception $e) {
                Log::error('Registration error: ' . $e->getMessage());
                return response()->json(['error' => 'Registration failed', 'message' => $e->getMessage()], 500);
            }
    }




    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }

    /**
     * Handle referral link access.
     */
    public function referralLink($code)
    {
        $referrer = User::where('referral_code', $code)->first();

        if (!$referrer) {
            return redirect()->route('landing')->with('error', 'Invalid referral code.');
        }

        // Store referral code in session for registration
        session(['referral_code' => $code]);

        return redirect()->route('register', ['ref' => $code])
                        ->with('success', "You've been referred by {$referrer->name}! Register now to get started.");
    }
}
