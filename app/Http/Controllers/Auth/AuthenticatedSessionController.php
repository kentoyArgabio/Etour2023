<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\SubsPerk;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        if(auth()->user()->type == 'agency' && auth()->user()->stripe_id != null && auth()->user()->subscription->ends_at == null){
            $addMonths = Carbon::parse(auth()->user()->subscription->created_at)->diffInMonths(Carbon::today());
            $date = Carbon::parse(auth()->user()->subscription->created_at)->addMonths($addMonths)->toDateString();
            if(Carbon::parse($date)->diffInDays(Carbon::today()) == 0){
                $package_counter = 0;
                if(auth()->user()->subscription->name == 'basic'){
                    $package_counter = 5;
                } else if(auth()->user()->subscription->name == 'plus'){
                    $package_counter = 10;
                } else {
                    $package_counter = 30;
                }

                auth()->user()->subsperk->update([
                    'package_counter' => $package_counter
                ]);
            }
            
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
