<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\TimeSlot;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function dashboard()
    {
        $pendingReservations = Reservation::where('status', Reservation::STATUS_PENDING)
            ->with(['user', 'timeSlot', 'field'])
            ->orderBy('created_at', 'desc')
            ->get();

        $paidReservations = Reservation::where('status', Reservation::STATUS_PAID)
            ->with(['user', 'timeSlot', 'field'])
            ->orderBy('created_at', 'desc')
            ->get();

        $approvedReservations = Reservation::where('status', Reservation::STATUS_APPROVED)
            ->with(['user', 'timeSlot', 'field'])
            ->orderBy('created_at', 'desc')
            ->get();

        $cancelledReservations = Reservation::where('status', Reservation::STATUS_CANCELLED)
            ->with(['user', 'timeSlot', 'field'])
            ->orderBy('created_at', 'desc')
            ->get();

        $expiredReservations = Reservation::where('status', Reservation::STATUS_EXPIRED)
            ->with(['user', 'timeSlot', 'field'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Statistics
        $totalRevenue = Reservation::where('status', Reservation::STATUS_APPROVED)
            ->sum('total_price');

        $todayReservations = Reservation::whereDate('created_at', today())->count();
        $monthReservations = Reservation::whereMonth('created_at', now()->month)->count();

        return view('admin.dashboard', compact(
            'pendingReservations',
            'paidReservations',
            'approvedReservations',
            'cancelledReservations',
            'expiredReservations',
            'totalRevenue',
            'todayReservations',
            'monthReservations'
        ));
    }

    public function allReservations()
    {
        $reservations = Reservation::with(['user', 'timeSlot', 'field'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.all-reservations', compact('reservations'));
    }

   public function approve($id)
{
    $reservation = Reservation::findOrFail($id);

    if ($reservation->status !== Reservation::STATUS_PAID) {
        return redirect()->back()->with('error', 'الحجز ليس في انتظار الموافقة');
    }

    $reservation->update([
        'status' => Reservation::STATUS_APPROVED
    ]);

    return redirect()->back()->with('success', 'تمت الموافقة على الحجز بنجاح');
}

public function markAsPaid($id)
{
    $reservation = Reservation::findOrFail($id);

    if ($reservation->status !== Reservation::STATUS_PENDING) {
        return redirect()->back()->with('error', 'لا يمكن تغيير الحالة');
    }

    $reservation->update([
        'status' => Reservation::STATUS_PAID
    ]);

    return redirect()->back()->with('success', 'تم تأكيد الدفع');
}





















    public function cancelReservation($id)
    {
        $reservation = Reservation::findOrFail($id);

        if (in_array($reservation->status, [Reservation::STATUS_APPROVED, Reservation::STATUS_EXPIRED])) {
            return redirect()->back()->with('error', 'لا يمكن إلغاء هذا الحجز');
        }

        // Make time slot available again
        $reservation->timeSlot->update(['is_available' => true]);

        $reservation->update(['status' => Reservation::STATUS_CANCELLED]);

        return redirect()->back()->with('success', 'تم إلغاء الحجز بنجاح');
    }

    // public function timeSlots()
    // {
    //     $field = Field::first();
        
    //     if (!$field) {
    //         $field = Field::create([
    //             'name' => 'ملعب كرة القدم',
    //             'location' => 'الموقع',
    //             'price_per_hour' => 100,
    //         ]);
    //     }

    //     $timeSlots = TimeSlot::where('field_id', $field->id)
    //         ->orderBy('date')
    //         ->orderBy('start_time')
    //         ->get()
    //         ->groupBy('date');

    //     return view('admin.time-slots', compact('field', 'timeSlots'));
    // }

    // public function createTimeSlots(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'start_date' => 'required|date|after_or_equal:today',
    //         'end_date' => 'required|date|after:start_date',
    //         'start_time' => 'required',
    //         'end_time' => 'required|after:start_time',
    //     ], [
    //         'start_date.required' => 'تاريخ البداية مطلوب',
    //         'end_date.required' => 'تاريخ النهاية مطلوب',
    //         'start_time.required' => 'وقت البداية مطلوب',
    //         'end_time.required' => 'وقت النهاية مطلوب',
    //     ]);

    //     if ($validator->fails()) {
    //         return redirect()->back()
    //             ->withErrors($validator)
    //             ->withInput();
    //     }

    //     $field = Field::first();
        
    //     if (!$field) {
    //         return redirect()->back()->with('error', 'الملعب غير موجود');
    //     }

    //     $startDate = \Carbon\Carbon::parse($request->start_date);
    //     $endDate = \Carbon\Carbon::parse($request->end_date);
    //     $startTime = $request->start_time;
    //     $endTime = $request->end_time;

    //     $created = 0;
    //     for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
    //         // Check if time slot already exists
    //         $exists = TimeSlot::where('field_id', $field->id)
    //             ->where('date', $date->toDateString())
    //             ->where('start_time', $startTime)
    //             ->exists();

    //         if (!$exists) {
    //             TimeSlot::create([
    //                 'field_id' => $field->id,
    //                 'date' => $date->toDateString(),
    //                 'start_time' => $startTime,
    //                 'end_time' => $endTime,
    //                 'is_available' => true,
    //             ]);
    //             $created++;
    //         }
    //     }

    //     return redirect()->back()->with('success', "تم إنشاء $created موعد بنجاح");
    // }

    // public function deleteTimeSlot($id)
    // {
    //     $timeSlot = TimeSlot::findOrFail($id);

    //     // Check if there are active reservations
    //     $hasActiveReservation = Reservation::where('time_slot_id', $timeSlot->id)
    //         ->whereIn('status', [Reservation::STATUS_PENDING, Reservation::STATUS_PAID, Reservation::STATUS_APPROVED])
    //         ->exists();

    //     if ($hasActiveReservation) {
    //         return redirect()->back()->with('error', 'لا يمكن حذف هذا الموعد لوجود حجز نشط');
    //     }

    //     $timeSlot->delete();

    //     return redirect()->back()->with('success', 'تم حذف الموعد بنجاح');
    // }

    public function updateField(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'price_per_hour' => 'required|numeric|min:0',
        ], [
            'name.required' => 'اسم الملعب مطلوب',
            'location.required' => 'موقع الملعب مطلوب',
            'price_per_hour.required' => 'سعر الساعة مطلوب',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $field = Field::first();
        
        if (!$field) {
            $field = Field::create([
                'name' => $request->name,
                'location' => $request->location,
                'price_per_hour' => $request->price_per_hour,
            ]);
        } else {
            $field->update([
                'name' => $request->name,
                'location' => $request->location,
                'price_per_hour' => $request->price_per_hour,
            ]);
        }

        return redirect()->back()->with('success', 'تم تحديث بيانات الملعب بنجاح');
    }

    public function statistics()
    {
        // Monthly revenue for the last 6 months
        $monthlyRevenue = [];
        $monthlyLabels = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $revenue = Reservation::where('status', Reservation::STATUS_APPROVED)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('total_price');
            
            $monthlyRevenue[] = $revenue;
            $monthlyLabels[] = $date->format('M');
        }

        // Daily reservations for the last 7 days
        $dailyReservations = [];
        $dailyLabels = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = Reservation::whereDate('created_at', $date->toDateString())->count();
            
            $dailyReservations[] = $count;
            $dailyLabels[] = $date->format('l');
        }

        $totalReservations = Reservation::count();
        $totalRevenue = Reservation::where('status', Reservation::STATUS_APPROVED)->sum('total_price');
        $approvedCount = Reservation::where('status', Reservation::STATUS_APPROVED)->count();
        $cancelledCount = Reservation::where('status', Reservation::STATUS_CANCELLED)->count();

        return view('admin.statistics', compact(
            'monthlyRevenue',
            'monthlyLabels',
            'dailyReservations',
            'dailyLabels',
            'totalReservations',
            'totalRevenue',
            'approvedCount',
            'cancelledCount'
        ));
    }
    public function reset($id)
{
    $reservation = Reservation::findOrFail($id);

    $reservation->timeSlot->update(['is_available' => true]);
    $reservation->update(['status' => 'cancelled']);

    return back()->with('success', 'تم إرجاع الموعد إلى متاح');
}
public function destroy($id)
{
    $reservation = Reservation::findOrFail($id);

    // رجّع الوقت متاح قبل الحذف
    $reservation->timeSlot->update(['is_available' => true]);

    $reservation->delete();

    return back()->with('success', 'تم حذف الطلب بنجاح');
}
public function edit($id)
{
    $reservation = Reservation::with('timeSlot')->findOrFail($id);

    $availableSlots = TimeSlot::where('is_available', true)->get();

    return view('admin.edit-reservation', compact('reservation', 'availableSlots'));
}
public function update(Request $request, $id)
{
    $reservation = Reservation::findOrFail($id);

    $reservation->update([
        'status' => $request->status
    ]);

    return redirect()->route('admin.reservations')
        ->with('success', 'تم تحديث الطلب بنجاح');
}
public function changeTime(Request $request, $id)
{
    $request->validate([
        'time_slot_id' => 'required|exists:time_slots,id'
    ]);

    $reservation = Reservation::findOrFail($id);
    $newSlot = TimeSlot::findOrFail($request->time_slot_id);

    // تأكد أن الوقت الجديد متاح
    if (!$newSlot->is_available) {
        return back()->with('error', 'هذا التوقيت غير متاح');
    }

    // رجّع الوقت القديم متاح
    $reservation->timeSlot->update([
        'is_available' => true
    ]);

    // اربط الحجز بالتوقيت الجديد
    $reservation->update([
        'time_slot_id' => $newSlot->id
    ]);

    // قفل التوقيت الجديد
    $newSlot->update([
        'is_available' => false
    ]);

    return back()->with('success', 'تم تغيير التوقيت بنجاح');
}
public function blockSlots(Request $request)
{
    $type = $request->type;

    if ($type === 'hours') {

        TimeSlot::where('date', $request->date)
            ->whereBetween('start_time', [$request->start_time, $request->end_time])
            ->update(['is_available' => 0]);
    }

    elseif ($type === 'day') {

        TimeSlot::where('date', $request->date)
            ->update(['is_available' => 0]);
    }

    elseif ($type === 'month') {

        $month = date('m', strtotime($request->date));
        $year = date('Y', strtotime($request->date));

        TimeSlot::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->update(['is_available' => 0]);
    }

    return back()->with('success', 'تم إيقاف الحجز بنجاح');
}



public function timeSlots(Request $request)
{
    $query = TimeSlot::query();

    // فلترة حسب التاريخ
    if ($request->has('date') && $request->date) {
        $query->where('date', $request->date);
    } else {
        $query->where('date', date('Y-m-d')); // الافتراضي اليوم
    }

    // فلترة حسب الحالة
    if ($request->has('status') && $request->status) {
        if ($request->status == 'available') {
            $query->where('is_available', true);
        } elseif ($request->status == 'booked') {
            $query->where('is_available', false);
        }
    }

    // جلب النتائج و ترتيبها
    $timeSlots = $query->orderBy('start_time')->get()->groupBy('date');

    return view('admin.time-slots', compact('timeSlots'));
}

public function createTimeSlots(Request $request)
{
    $request->validate([
        'start_date'=>'required|date',
        'end_date'=>'required|date|after_or_equal:start_date',
        'start_time'=>'required',
        'end_time'=>'required|after:start_time',
    ]);

    $period = new \DatePeriod(
        new \DateTime($request->start_date),
        new \DateInterval('P1D'),
        (new \DateTime($request->end_date))->modify('+1 day')
    );

    foreach ($period as $date) {
        TimeSlot::create([
            'date' => $date->format('Y-m-d'),
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_available' => true,
        ]);
    }

    return redirect()->route('admin.time-slots')->with('success','تم إنشاء المواعيد بنجاح');
}

public function deleteTimeSlot($id)
{
    $slot = TimeSlot::findOrFail($id);

    if($slot->is_available){
        $slot->delete();
        return back()->with('success','تم حذف الموعد');
    }

    return back()->with('error','لا يمكن حذف موعد محجوز');
}

// public function updateTimeSlot(Request $request, $id)
// {
//     $slot = TimeSlot::findOrFail($id);

//     $request->validate([
//         'start_time'=>'required',
//         'end_time'=>'required|after:start_time',
//         'is_available'=>'required|boolean'
//     ]);

//     $slot->update([
//         'start_time'=>$request->start_time,
//         'end_time'=>$request->end_time,
//         'is_available'=>$request->is_available
//     ]);

//     return back()->with('success','تم تعديل الموعد بنجاح');
// }

public function updateTimeSlot(Request $request, $id)
{
    $slot = TimeSlot::findOrFail($id);

    $slot->start_time = $request->start_time;
    $slot->end_time = $request->end_time;
    $slot->is_available = $request->is_available;
    $slot->save();

    // رجوع للصفحة مع رسالة نجاح
    return redirect()->route('admin.time-slots')
                     ->with('success', 'تم تعديل الموعد بنجاح');
}
}
