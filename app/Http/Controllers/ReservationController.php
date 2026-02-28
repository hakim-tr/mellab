<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
class ReservationController extends Controller
{
    public function myReservations()
{
    $userId = session('user_id'); // جلب المستخدم من session
    $user = User::find($userId);

    if (!$user) {
        return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
    }

    $reservations = Reservation::with(['field', 'timeSlot'])
                        ->where('user_id', $user->id)
                        ->orderBy('created_at', 'desc')
                        ->get();

    return view('client.reservations', compact('reservations'));
}
 public function reservations()
    {
 $userId = session('user_id'); // جلب المستخدم من session
    $user = User::find($userId);
        $reservations = Reservation::where('user_id', $user->id)
            ->with(['timeSlot', 'field'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('client.reservations', compact('reservations'));
    }

public function generatePdf($reservationId)
{
    $reservation = Reservation::with('timeSlot', 'user', 'field')->findOrFail($reservationId);

    $pdf = Pdf::loadView('client.reservation-pdf', compact('reservation'))
              ->setPaper('A4', 'portrait');

    return $pdf->download('Reservation_'.$reservation->id.'.pdf');
}
}
