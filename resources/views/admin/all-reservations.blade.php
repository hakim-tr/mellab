@extends('layouts.main')

@section('title', 'جميع الحجوزات')

@section('content')

<div class="container py-3">

    <!-- عنوان + بحث -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h2><i class="fas fa-list"></i> جميع الحجوزات</h2>
        <form method="GET" action="{{ route('admin.reservations') }}" class="d-flex gap-2 flex-wrap">
            <input type="date" name="date" class="form-control" value="{{ request('date') }}" placeholder="تاريخ">
            <select name="status" class="form-select">
                <option value="">الحالة</option>
                <option value="pending" {{ request('status')=='pending'?'selected':'' }}>في انتظار الدفع</option>
                <option value="paid" {{ request('status')=='paid'?'selected':'' }}>في انتظار الموافقة</option>
                <option value="approved" {{ request('status')=='approved'?'selected':'' }}>موافق عليه</option>
                <option value="expired" {{ request('status')=='expired'?'selected':'' }}>منتهي</option>
                <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>ملغى</option>
            </select>
            <button type="submit" class="btn btn-primary">بحث</button>
        </form>
    </div>

    <!-- جدول للشاشات الكبيرة -->
    <div class="card shadow-sm d-none d-md-block">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>المستخدم</th>
                            <th>التاريخ</th>
                            <th>الوقت</th>
                            <th>السعر</th>
                            <th>الحالة</th>
                            <th>قبول</th>
                            <th>إلغاء</th>
                            <th>تم الدفع</th>
                            <th>إرجاع متاح</th>
                            <th>حذف</th>
                            <th>تعديل</th>
                            <th>🕒 تعديل التوقيت</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reservations as $reservation)
                        <tr>
                            <td>{{ $reservation->user->name }}</td>
                            <td>{{ $reservation->timeSlot->date }}</td>
                            <td>{{ $reservation->timeSlot->start_time }}</td>
                            <td>{{ $reservation->total_price }} درهم</td>
                            <td>
                                @if($reservation->status == 'pending')
                                    <span class="badge bg-warning">في انتظار الدفع</span>
                                @elseif($reservation->status == 'paid')
                                    <span class="badge bg-info">في انتظار الموافقة</span>
                                @elseif($reservation->status == 'approved')
                                    <span class="badge bg-success">موافق عليه</span>
                                @elseif($reservation->status == 'expired')
                                    <span class="badge bg-danger">منتهي</span>
                                @elseif($reservation->status == 'cancelled')
                                    <span class="badge bg-secondary">ملغى</span>
                                @endif
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.approve', $reservation->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">قبول</button>
                                </form>
                            </td>
                            <td>
                                @if(in_array($reservation->status, ['pending', 'paid']))
                                <form method="POST" action="{{ route('admin.cancel', $reservation->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">إلغاء</button>
                                </form>
                                @endif
                            </td>
                            <td>
                                @if($reservation->status == 'pending')
                                <form method="POST" action="{{ route('admin.markPaid', $reservation->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm">تم الدفع</button>
                                </form>
                                @endif
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.reset', $reservation->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary btn-sm">🔄 إرجاع متاح</button>
                                </form>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.reservations.destroy', $reservation->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">🗑 حذف</button>
                                </form>
                            </td>
                            <td>
                                <a href="{{ route('admin.reservations.edit', $reservation->id) }}" class="btn btn-warning btn-sm">✏ تعديل</a>
                            </td>
                            <td>
                                <a href="{{ route('admin.reservations.edit', $reservation->id) }}" class="btn btn-info btn-sm">🕒 تعديل التوقيت</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- بطاقات للهواتف -->
    <div class="d-md-none mt-4">
        @foreach($reservations as $reservation)
        <div class="reservation-card shadow-sm p-3 mb-3 rounded">
            <h5>{{ $reservation->user->name }}</h5>
            <p><strong>التاريخ:</strong> {{ $reservation->timeSlot->date }}</p>
            <p><strong>الوقت:</strong> {{ $reservation->timeSlot->start_time }}</p>
            <p><strong>السعر:</strong> {{ $reservation->total_price }} درهم</p>
            <p>
                <strong>الحالة:</strong>
                @if($reservation->status == 'pending')
                    <span class="badge bg-warning">في انتظار الدفع</span>
                @elseif($reservation->status == 'paid')
                    <span class="badge bg-info">في انتظار الموافقة</span>
                @elseif($reservation->status == 'approved')
                    <span class="badge bg-success">موافق عليه</span>
                @elseif($reservation->status == 'expired')
                    <span class="badge bg-danger">منتهي</span>
                @elseif($reservation->status == 'cancelled')
                    <span class="badge bg-secondary">ملغى</span>
                @endif
            </p>
            <div class="d-flex flex-wrap gap-2 mt-2">
                <form method="POST" action="{{ route('admin.approve', $reservation->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm">قبول</button>
                </form>
                @if(in_array($reservation->status, ['pending', 'paid']))
                <form method="POST" action="{{ route('admin.cancel', $reservation->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">إلغاء</button>
                </form>
                @endif
                @if($reservation->status == 'pending')
                <form method="POST" action="{{ route('admin.markPaid', $reservation->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm">تم الدفع</button>
                </form>
                @endif
                <form method="POST" action="{{ route('admin.reset', $reservation->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-sm">🔄 إرجاع متاح</button>
                </form>
                <form method="POST" action="{{ route('admin.reservations.destroy', $reservation->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">🗑 حذف</button>
                </form>
                <a href="{{ route('admin.reservations.edit', $reservation->id) }}" class="btn btn-warning btn-sm">✏ تعديل</a>
                <a href="{{ route('admin.reservations.edit', $reservation->id) }}" class="btn btn-info btn-sm">🕒 تعديل التوقيت</a>
            </div>
        </div>
        @endforeach
    </div>

</div>

@endsection