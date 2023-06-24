<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Agency;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Redirect;
use App\Notifications\NewUserNotification;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Support\Facades\Notification;

class AgencyController extends Controller
{
    public function create()
    {
        return view('auth.register-agency');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'agency' => ['required', 'string', 'max:255', 'min:3'],
            'description' => ['required', 'string', 'max:255', 'min:6'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'type' => 'agency',
            'password' => Hash::make($request->password),
        ]);

        Agency::create([
            'user_id' => $user->id,
            'name' => $request->agency,
            'description' => $request->description,
        ]);

        event(new Registered($user));

        $admins = User::where('type', 'admin')->get();
        
        Notification::send($admins, new NewUserNotification($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }

    public function update(ProfileUpdateRequest $request)
    {
        $request->user()->fill($request->validated());
        
        $request->validate([
            'agency' => ['required', 'string', 'max:255', 'min:3'],
            'description' => ['required', 'string', 'max:255', 'min:6'],
        ]);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }
        
        $request->user()->save();
        $request->user()->agency->update([
            'name' => $request->agency,
            'description' => $request->description
        ]);

        toast('Agency information updated successfully','info');
        return Redirect::route('profile.edit');
    }
}
