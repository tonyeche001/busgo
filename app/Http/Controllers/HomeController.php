<?php

namespace App\Http\Controllers;
use App\Models\BusRoute;
use App\Models\Trip;
use App\Models\Booking;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $routes = BusRoute::where('is_active', true)->get();
        return view('user.dashboard', compact('routes'));

    }
    public function search(Request $request)
    {
        $request->validate([
            'from' => 'required|string',
            'to' => 'required|string',
            'date' => 'required|date',
        ]);
        $trips = Trip::with(['busRoute','bus'])
        ->withCount(['bookings' => fn ($q) => $q->where('status', '!=', 'cancelled')])
        ->where('status', 'scheduled')
        ->where('departure_at', '>', now())
        ->whereDate('departure_at', $request->date)
        ->whereHas('busRoute', fn($q) => $q
        ->where('is_active', true)
        ->where('origin', $request->from)
        ->where('destination', $request->to))
        ->orderBy('departure_at')
        ->get();
        return view('user.search', compact('trips'));
    }
    public function seats(Trip $trip)
    {
        $trip->load(['busRoute', 'bus']);
        $takenSeats = $trip->bookings()
        ->where('status', '!=', 'cancelled')
        ->pluck('seat_number')
        ->all();
        return view('user.seat', compact('trip','takenSeats'));
    }
    public function book(Request $request, Trip $trip)
    {
        $data = $request->validate([
            'seats' => 'required|array|min:1',
            'seats.*' => 'integer|min:1|max:' . $trip->bus->seat_capacity,
            'passenger_name' => 'required|string|max:255',
            'passenger_phone' => 'required|string|max:20',
        ], [
            'seats.required' => 'Please select atleast one seat.',
        ]);
        $codes = [];
        try{
            DB::transaction(function () use ($data, $trip, &$codes) {
                foreach($data['seats'] as $seat) {
                    $booking = Booking::create([
                        'booking_code' => 'BK' .strtoupper(Str::random(8)),
                        'trip_id' => $trip->id,
                        'user_id' => auth()->id(),
                        'passenger_name' => $data['passenger_name'],
                        'passenger_phone' => $data['passenger_phone'],
                        'seat_number' => $seat,
                        'amount' => $trip->fare,
                        'status' => 'pending',
                    ]);
                    $codes[] = $booking->booking_code;
                }
            });
        }catch (QueryException $e) {
            return back()->withInput()->withErrors([
                'seats' => 'Sorry,one of those seat was just booked.Please choose again.',
            ]);
        }
        session(['booking_codes' => $codes]);
        return redirect()->route('bookings.confirmation');
    }
    public function confirmation()
    {
        $codes = session('booking_codes', []);
        abort_if(empty($codes), 404);
        $bookings = Booking::with('trip.busRoute')
        ->whereIn('booking_code', $codes)
        ->orderBy('seat_number')
        ->get();
        return view('user.confirmation', compact('bookings'));
    }
    public function pay(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        $codes = session('booking_codes', []);
        abort_if(empty($codes), 404);
        $booking = Booking::whereIn('booking_code', $codes)
        ->where('status', 'pending')
        ->get();
        if($booking->isEmpty()) {
            return redirect()->route('home');
        }
        $reference = 'PAY-' . strtoupper(Str::random(12));
        Booking::whereIn('id', $booking->pluck('id'))->update(['payment_reference' => $reference]);

        $response = Http::withToken(config('services.paystack.secret'))
        ->post('https://api.paystack.co/transaction/initialize', [
            'email' => $request->email,
            'amount' => $booking->sum('amount') * 100,
            'reference' => $reference,
            'callback_url' => url('/payment/callback'),
        ]);
        if(! $response->successful() || ! $response->json('status')) {
            return back()->withErrors(['email' => 'Could not start the payment.Please try again.' .($response->json('message') ?? $response->status())]);
        }
        return redirect()->away($response->json('data.authorization_url'));

    }
    public function paymentCallback(Request $request)
    {
        $reference = $request->query('reference');
        abort_if(! $reference, 404);
        $bookings = Booking::where('payment_reference', $reference)->get();
        abort_if($bookings->isEmpty(), 404);
        session(['booking_codes' => $bookings->pluck('booking_code')->all()]);

        if($bookings->every(fn ($b) => $b->status == 'paid')) {
            return redirect()->route('bookings.confirmation');
        }
        $response = Http::withToken(config('services.paystack.secret'))
        ->get('https://api.paystack.co/transaction/verify/' . $reference);
        $paid = $response->successful()
        && $response->json('data.status') == 'success'
        &&(int) $response->json('data.amount') == (int) ($bookings->sum('amount') * 100);

        if(! $paid) {
            return redirect()->route('bookings.confirmation')
            ->withErrors(['email' => 'Payment was not completed. Your seat are still reserved, so please try again.']);

        }
        Booking::where('payment_reference', $reference)
        ->update(['status' => 'paid', 'paid_at' =>now()]);

        return redirect()->route('bookings.confirmation')
        ->with('success', 'Payment successful! Your seat are confirmed.');
    }
    public function findTicket() 
    {
        return view('user.ticket-find');
    }
    public function showTicket(Request $request)
    {
        $data = $request->validate([
            'booking_code' => 'required|string',
            'passenger_phone' => 'required|string',
        ]);
        $booking = Booking::with('trip.busRoute',  'trip.bus')

        ->where('booking_code', strtoupper(trim($data['booking_code'])))
        ->where('passenger_phone', trim($data['passenger_phone']))
        ->first();
        if(!$booking) {
            return back()->withInput()->withErrors(['booking_code' => 'No booking found with those details']);

    }

    if ($booking->status!=='paid') {
        return back()->withInput()->withErrors(['booking_code' => 'This booking has not been paid for yet.']);


    }
    return view('user.ticket', compact('booking'));
}

}