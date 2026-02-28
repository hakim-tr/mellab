<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\TimeSlot;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function index()
    {
        $field = Field::first();
        
        if (!$field) {
            // Create default field if not exists
            $field = Field::create([
                'name' => 'ملعب كرة القدم',
                'location' => 'الموقع',
                'price_per_hour' => 100,
            ]);
        }

        $today = now()->toDateString();
        $timeSlots = TimeSlot::where('field_id', $field->id)
            ->where('date', '>=', $today)
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->groupBy('date');

        return view('booking.index', compact('field', 'timeSlots'));
    }

    public function getAvailableSlots(Request $request)
    {
        $date = $request->date;
        $field = Field::first();

        if (!$field) {
            return response()->json(['error' => 'الملعب غير موجود'], 404);
        }

        $timeSlots = TimeSlot::where('field_id', $field->id)
            ->where('date', $date)
            ->orderBy('start_time')
            ->get();

        return response()->json($timeSlots);
    }

    public function reserve(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'time_slot_id' => 'required|exists:time_slots,id',
        ], [
            'time_slot_id.required' => 'الرجاء اختيار موعد',
            'time_slot_id.exists' => 'الموعد المحدد غير صالح',
        ]);

        if ($validator->fails()) {
            return redirect()->route('home')
                ->withErrors($validator);
        }

        $timeSlot = TimeSlot::findOrFail($request->time_slot_id);

        if (!$timeSlot->is_available) {
            return redirect()->route('home')
                ->with('error', 'هذا الموعد غير متاح');
        }

        // Check if user already has a reservation for this date
        $userId = session('user_id');
        // $existingReservation = Reservation::where('user_id', $userId)
        //     ->whereHas('timeSlot', function ($query) use ($timeSlot) {
        //         $query->where('date', $timeSlot->date);
        //     })
        //     ->whereIn('status', [Reservation::STATUS_PENDING, Reservation::STATUS_PAID, Reservation::STATUS_APPROVED])
        //     ->first();

        // if ($existingReservation) {
        //     return redirect()->route('home')
        //         ->with('error', 'لديك حجز لهذا اليوم بالفعل');
        // }

        $field = Field::first();
        $totalPrice = $field->price_per_hour;

        // Create reservation
        $reservation = Reservation::create([
            'user_id' => $userId,
            'field_id' => $field->id,
            'time_slot_id' => $timeSlot->id,
            'total_price' => $totalPrice,
            'status' => Reservation::STATUS_PENDING,
            'expires_at' => now()->addHours(24),
        ]);

        // Mark time slot as unavailable
        $timeSlot->update(['is_available' => false]);

        return redirect()->route('home')
            ->with('success', 'لقد تم إنشاء الحجز بنجاح، يجب الدفع خلال 24 ساعة أو سيتم إلغاؤه تلقائياً');
    }

    public function myReservations()
    {
        $userId = session('user_id');
        $reservations = Reservation::where('user_id', $userId)
            ->with(['timeSlot', 'field'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('booking.my-reservations', compact('reservations'));
    }

    public function pay(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reservation_id' => 'required|exists:reservations,id',
        ], [
            'reservation_id.required' => 'الحجز غير صالح',
        ]);

        if ($validator->fails()) {
            return redirect()->route('my.reservations')
                ->withErrors($validator);
        }

        $reservation = Reservation::findOrFail($request->reservation_id);

        if ($reservation->user_id != session('user_id')) {
            return redirect()->route('my.reservations')
                ->with('error', 'غير مصرح لك');
        }

        if ($reservation->status !== Reservation::STATUS_PENDING) {
            return redirect()->route('my.reservations')
                ->with('error', 'الحجز ليس في حالة الانتظار');
        }

        if ($reservation->isExpired()) {
            return redirect()->route('my.reservations')
                ->with('error', 'انتهت مهلة الحجز');
        }

        // Simulate payment - in real app, integrate with payment gateway
        $reservation->update(['status' => Reservation::STATUS_PAID]);

        return redirect()->route('my.reservations')
            ->with('success', 'تم الدفع بنجاح، في انتظار موافقة الإدارة');
    }

    public function cancel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reservation_id' => 'required|exists:reservations,id',
        ], [
            'reservation_id.required' => 'الحجز غير صالح',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $reservation = Reservation::findOrFail($request->reservation_id);

        if ($reservation->user_id != session('user_id')) {
            return redirect()->back()
                ->with('error', 'غير مصرح لك');
        }

        if (!in_array($reservation->status, [Reservation::STATUS_PENDING, Reservation::STATUS_PAID])) {
            return redirect()->back()
                ->with('error', 'لا يمكن إلغاء هذا الحجز');
        }

        // Make time slot available again
        $reservation->timeSlot->update(['is_available' => true]);

        $reservation->update(['status' => Reservation::STATUS_CANCELLED]);

        return redirect()->back()
            ->with('success', 'تم إلغاء الحجز بنجاح');
    }

    
}
