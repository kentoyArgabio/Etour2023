<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Agency;
use App\Models\Booking;
use App\Models\Feedback;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\SubsPerk;
use Laravel\Cashier\Subscription;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => User::find($request->user()->id),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        toast('Profile updated successfully','success');
        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();
        if(auth()->user()->agency != null && auth()->user()->agency->travelPackages->count() > 0){
            auth()->user()->agency->travelPackages->map(function($data){
                $data->timeslots->delete();
                $data->locations->delete();
                $data->packageTypes->delete();
            });
            auth()->user()->agency->travelPackages->delete();
            auth()->user()->agency->delete();
        }
        Feedback::where('user_id', auth()->user()->id)->delete();
        Booking::where('user_id', auth()->user()->id)->delete();
        auth()->user()->notifications()->delete();
        Subscription::where('user_id', auth()->user()->id)->delete();
        SubsPerk::where('user_id', auth()->user()->id)->delete();
        


        Auth::logout();

        $user->delete();


        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function notifications()
    {
        return view('profile.notifications');
    }

    public function readNotifications()
    {
        auth()->user()->unreadNotifications->markAsRead();

        toast('Notifications updated successfully','success');
        return back();
    }

    public function profile(User $user)
    {
        if(!auth()->user()->type == 'admin') {
            abort(403, 'Unauthorized Action');
        }

        return view('profile.index', [
            'user' => $user
        ]);
    }

    public function deleteUser(User $user)
    {
        $user->delete();

        toast('User deleted successfully','warning');
        return redirect('/dashboard');
    }

    public function users()
    {
        if(!auth()->user()->type == 'admin') {
            abort(403, 'Unauthorized Action');
        }

        return view('profile.users', [
            'users' => User::latest()
            ->filter(request(['search']))
            ->paginate(6)
        ]);
    }

    public function deleteNotifications()
    {
        auth()->user()->notifications()->delete();

        toast('Notifications deleted successfully','warning');

        return back();
    }

}
