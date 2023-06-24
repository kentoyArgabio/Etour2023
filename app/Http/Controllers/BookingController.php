<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Feedback;
use Illuminate\Http\Request;
use App\Notifications\ConfirmBooking;
use Illuminate\Support\Facades\Notification;

class BookingController extends Controller
{
    public function confirmBooking(Booking $booking)
    {

        $booking->update([
            'status' => 'confirmed'
        ]);

        //Send email and notification to traveler that the booking is confirmed and can now post a feeback.
        Notification::send($booking->user, new ConfirmBooking($booking->travelPackage));

        toast('Booking confirmed successfully!','success');
        return back();
    }

    public function storeFeedback(Request $request)
    {

        $request->validate([
            'stars' => 'required|integer|min:1|max:5',
            'message' => 'required|string|max:255'
        ]);

        $booking = Booking::where('id', $request->booking_id)->get()->first();

        $booking->update([
            'reviewed' => true,
        ]);

        Feedback::create([
            'user_id' => auth()->user()->id,
            'travel_package_id' => $request->travel_package_id,
            'stars' => $request->stars,
            'message' => $request->message
        ]);



        toast('Feedback posted successfully!','success');
        return back();
    }
}
