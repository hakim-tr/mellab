@extends('layouts.main')

@section('title', 'حجوزاتي')

@section('content')
<style>
  
body {
    background: linear-gradient(135deg, #ffffffff, #f8f8f8ff, #f7f7f7ff);
}    
/* Badges حسب الحالة */
.slot-pending { background-color: #ffc107; color: #000; }
.slot-paid { background-color: #0dcaf0; color: #000; }
.slot-approved { background-color: #198754; color: #fff; }
.slot-expired { background-color: #dc3545; color: #fff; }
.slot-cancelled { background-color: #6c757d; color: #fff; }

/* Responsive cards */
.reservation-card {
    border: 1px solid #eee;
    border-radius: 12px;
    padding: 12px;
    margin-bottom: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.reservation-card h5 {
    font-size: 1rem;
    margin-bottom: 5px;
}
.reservation-card p {
    margin: 2px 0;
    font-size: 0.9rem;
}

/* Buttons spacing */
.reservation-actions button {
    margin-right: 5px;
    margin-top: 5px;
}

/* Table for larger screens */
@media (min-width: 768px) {
    .reservation-card { display: none; }
}
@media (max-width: 767px) {
    .table-responsive { display: none; }
}
</style>

<h2 class="mb-3"><i class="fas fa-calendar-alt"></i> حجوزاتي</h2>

@if($reservations->isEmpty())
    <div class="alert alert-info">
        لا توجد حجوزات سابقة
    </div>
@else
    <!-- Table للكمبيوتر -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>رقم الحجز</th>
                    <th>التاريخ</th>
                    <th>الوقت</th>
                    <th>السعر</th>
                    <th>الحالة</th>
                    <th>تاريخ الانتهاء</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reservations as $reservation)
                <tr>
                    <td>{{ $reservation->id }}</td>
                    <td>{{ $reservation->timeSlot->date }}</td>
                    <td>{{ $reservation->timeSlot->start_time }} - {{ $reservation->timeSlot->end_time }}</td>
                    <td>{{ $reservation->total_price }} ريال</td>
                    <td>
                        @switch($reservation->status)
                            @case('pending')
                                <span class="badge slot-pending">قيد الانتظار</span>
                                @break
                            @case('paid')
                                <span class="badge slot-paid">في انتظار الموافقة</span>
                                @break
                            @case('approved')
                                <span class="badge slot-approved">موافق عليه</span>
                                @break
                            @case('expired')
                                <span class="badge slot-expired">منتهي الصلاحية</span>
                                @break
                            @case('cancelled')
                                <span class="badge slot-cancelled">ملغى</span>
                                @break
                        @endswitch
                    </td>
                    <td>{{ $reservation->expires_at ? $reservation->expires_at->format('Y-m-d H:i') : '-' }}</td>
                    <td class="reservation-actions">
                        @if($reservation->status === 'pending')
                            <form method="POST" action="{{ route('pay') }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                                <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-credit-card"></i> دفع</button>
                            </form>
                            <form method="POST" action="{{ route('cancel') }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من إلغاء الحجز؟')">
                                    <i class="fas fa-times"></i> إلغاء
                                </button>
                            </form>
                        @elseif($reservation->status === 'paid')
                            <form method="POST" action="{{ route('cancel') }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من إلغاء الحجز؟')">
                                    <i class="fas fa-times"></i> إلغاء
                                </button>
                            </form>
                        @else
                            <span class="text-muted">لا توجد إجراءات</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Cards للهاتف -->
    @foreach($reservations as $reservation)
    <div class="reservation-card">
        <h5>رقم الحجز: {{ $reservation->id }}</h5>
        <p>التاريخ: {{ $reservation->timeSlot->date }}</p>
        <p>الوقت: {{ $reservation->timeSlot->start_time }} - {{ $reservation->timeSlot->end_time }}</p>
        <p>السعر: {{ $reservation->total_price }} ريال</p>
        <p>
            الحالة: 
            @switch($reservation->status)
                @case('pending')
                    <span class="badge slot-pending">قيد الانتظار</span>
                    @break
                @case('paid')
                    <span class="badge slot-paid">في انتظار الموافقة</span>
                    @break
                @case('approved')
                    <span class="badge slot-approved">موافق عليه</span>
                    @break
                @case('expired')
                    <span class="badge slot-expired">منتهي الصلاحية</span>
                    @break
                @case('cancelled')
                    <span class="badge slot-cancelled">ملغى</span>
                    @break
            @endswitch
        </p>
        <p>تاريخ الانتهاء: {{ $reservation->expires_at ? $reservation->expires_at->format('Y-m-d H:i') : '-' }}</p>
        <div class="reservation-actions">
            @if($reservation->status === 'pending')
                <form method="POST" action="{{ route('pay') }}" class="d-inline">
                    @csrf
                    <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                    <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-credit-card"></i> دفع</button>
                </form>
                <form method="POST" action="{{ route('cancel') }}" class="d-inline">
                    @csrf
                    <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من إلغاء الحجز؟')">
                        <i class="fas fa-times"></i> إلغاء
                    </button>
                </form>
            @elseif($reservation->status === 'paid')
                <form method="POST" action="{{ route('cancel') }}" class="d-inline">
                    @csrf
                    <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من إلغاء الحجز؟')">
                        <i class="fas fa-times"></i> إلغاء
                    </button>
                </form>
            @else
                <span class="text-muted">لا توجد إجراءات</span>
            @endif
        </div>
    </div>
    @endforeach
@endif

<div class="mt-3">
    <a href="{{ route('home') }}" class="btn btn-primary">
        <i class="fas fa-arrow-right"></i> العودة للحجوزات
    </a>
</div>
@endsection