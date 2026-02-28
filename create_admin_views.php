<?php

// Create admin directory if it doesn't exist
$adminDir = 'resources/views/admin';
if (!is_dir($adminDir)) {
    mkdir($adminDir, 0755, true);
}

$views = [
    'dashboard' => '@extends(\'layouts.main\')

@section(\'title\', \'لوحة تحكم الإدارة\')


@section(\'content\')
<div class="row">
    <div class="col-md-12">
        <h2><i class="fas fa-tachometer-alt"></i> لوحة تحكم الإدارة</h2>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-3">
        <div class="card stat-card bg-primary text-white">
            <div class="card-body">
                <h5><i class="fas fa-money-bill"></i> إجمالي الإيرادات</h5>
                <h3>{{ $totalRevenue }} ريال</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card bg-info text-white">
            <div class="card-body">
                <h5><i class="fas fa-calendar-day"></i> حجوزات اليوم</h5>
                <h3>{{ $todayReservations }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card bg-warning text-dark">
            <div class="card-body">
                <h5><i class="fas fa-calendar-month"></i> حجوزات الشهر</h5>
                <h3>{{ $monthReservations }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card bg-success text-white">
            <div class="card-body">
                <h5><i class="fas fa-check-circle"></i> الموافقات</h5>
                <h3>{{ $approvedReservations->count() }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>روابط سريعة</h5>
            </div>
            <div class="card-body">
                <a href="{{ route(\'admin.reservations\') }}" class="btn btn-primary">
                    <i class="fas fa-list"></i> عرض جميع الحجوزات
                </a>
                <a href="{{ route(\'admin.time-slots\') }}" class="btn btn-success">
                    <i class="fas fa-clock"></i> إدارة المواعيد
                </a>
                <a href="{{ route(\'admin.statistics\') }}" class="btn btn-info">
                    <i class="fas fa-chart-bar"></i> الإحصائيات
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-warning">
                <h5><i class="fas fa-clock"></i> حجوزات في انتظار الدفع</h5>
            </div>
            <div class="card-body">
                @if($pendingReservations->isEmpty())
                    <p>لا توجد حجوزات في انتظار الدفع</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>المستخدم</th>
                                    <th>التاريخ</th>
                                    <th>الوقت</th>
                                    <th>السعر</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingReservations as $reservation)
                                <tr>
                                    <td>{{ $reservation->user->name }}</td>
                                    <td>{{ $reservation->timeSlot->date }}</td>
                                    <td>{{ $reservation->timeSlot->start_time }}</td>
                                    <td>{{ $reservation->total_price }} ريال</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5><i class="fas fa-credit-card"></i> حجوزات في انتظار الموافقة</h5>
            </div>
            <div class="card-body">
                @if($paidReservations->isEmpty())
                    <p>لا توجد حجوزات في انتظار الموافقة</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>المستخدم</th>
                                    <th>التاريخ</th>
                                    <th>الوقت</th>
                                    <th>السعر</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paidReservations as $reservation)
                                <tr>
                                    <td>{{ $reservation->user->name }}</td>
                                    <td>{{ $reservation->timeSlot->date }}</td>
                                    <td>{{ $reservation->timeSlot->start_time }}</td>
                                    <td>{{ $reservation->total_price }} ريال</td>
                                    <td>
                                        <form method="POST" action="{{ route(\'admin.approve\', $reservation->id) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">قبول</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection',

    'all-reservations' => '@extends(\'layouts.main\')

@section(\'title\', \'جميع الحجوزات\')


@section(\'content\')
<div class="row">
    <div class="col-md-12">
        <h2><i class="fas fa-list"></i> جميع الحجوزات</h2>
    </div>
</div>

@if($reservations->isEmpty())
    <div class="alert alert-info">
        <p>لا توجد حجوزات</p>
    </div>
@else
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>رقم الحجز</th>
                    <th>المستخدم</th>
                    <th>التاريخ</th>
                    <th>الوقت</th>
                    <th>السعر</th>
                    <th>الحالة</th>
                    <th>تاريخ الإنشاء</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reservations as $reservation)
                <tr>
                    <td>{{ $reservation->id }}</td>
                    <td>{{ $reservation->user->name }}</td>
                    <td>{{ $reservation->timeSlot->date }}</td>
                    <td>{{ $reservation->timeSlot->start_time }} - {{ $reservation->timeSlot->end_time }}</td>
                    <td>{{ $reservation->total_price }} ريال</td>
                    <td>
                        @switch($reservation->status)
                            @case(\'pending\')
                                <span class="badge slot-pending">قيد الانتظار</span>
                                @break
                            @case(\'paid\')
                                <span class="badge slot-paid">في انتظار الموافقة</span>
                                @break
                            @case(\'approved\')
                                <span class="badge slot-approved">موافق عليه</span>
                                @break
                            @case(\'expired\')
                                <span class="badge slot-expired">منتهي الصلاحية</span>
                                @break
                            @case(\'cancelled\')
                                <span class="badge slot-cancelled">ملغى</span>
                                @break
                        @endswitch
                    </td>
                    <td>{{ $reservation->created_at->format(\'Y-m-d H:i\') }}</td>
                    <td>
                        @if($reservation->status === \'paid\')
                            <form method="POST" action="{{ route(\'admin.approve\', $reservation->id) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">قبول</button>
                            </form>
                            <form method="POST" action="{{ route(\'admin.cancel\', $reservation->id) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'هل أنت متأكد؟\')">رفض</button>
                            </form>
                        @elseif(in_array($reservation->status, [\'pending\', \'paid\']))
                            <form method="POST" action="{{ route(\'admin.cancel\', $reservation->id) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'هل أنت متأكد؟\')">إلغاء</button>
                            </form>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

<div class="mt-3">
    <a href="{{ route(\'admin.dashboard\') }}" class="btn btn-primary">
        <i class="fas fa-arrow-right"></i> العودة للوحة التحكم
    </a>
</div>
@endsection',

    'time-slots' => '@extends(\'layouts.main\')

@section(\'title\', \'إدارة المواعيد\')


@section(\'content\')
<div class="row">
    <div class="col-md-12">
        <h2><i class="fas fa-clock"></i> إدارة المواعيد</h2>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5>معلومات الملعب</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route(\'admin.update-field\') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>اسم الملعب</label>
                                <input type="text" name="name" class="form-control" value="{{ $field->name }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>الموقع</label>
                                <input type="text" name="location" class="form-control" value="{{ $field->location }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>السعر لكل ساعة</label>
                                <input type="number" name="price_per_hour" class="form-control" value="{{ $field->price_per_hour }}">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">تحديث البيانات</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5>إنشاء مواعيد جديدة</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route(\'admin.create-time-slots\') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label>تاريخ البداية</label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label>تاريخ النهاية</label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label>وقت البداية</label>
                                <input type="time" name="start_time" class="form-control" value="08:00" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label>وقت النهاية</label>
                                <input type="time" name="end_time" class="form-control" value="22:00" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-success form-control">إنشاء</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>المواعيد المتاحة</h5>
            </div>
            <div class="card-body">
                @if($timeSlots->isEmpty())
                    <p>لا توجد مواعيد</p>
                @else
                    @foreach($timeSlots as $date => $slots)
                        <h5 class="mt-3">{{ $date }}</h5>
                        <div class="row">
                            @foreach($slots as $slot)
                            <div class="col-md-2 mb-2">
                                <div class="card {{ $slot->is_available ? \'slot-available\' : \'slot-unavailable\' }}">
                                    <div class="card-body p-2 text-center">
                                        <h6>{{ $slot->start_time }} - {{ $slot->end_time }}</h6>
                                        @if($slot->is_available)
                                            <span class="badge bg-success">متاح</span>
                                        @else
                                            <span class="badge bg-danger">محجوز</span>
                                        @endif
                                        @if($slot->is_available)
                                            <form method="POST" action="{{ route(\'admin.delete-time-slot\', $slot->id) }}" class="mt-1">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'هل أنت متأكد؟\')">حذف</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route(\'admin.dashboard\') }}" class="btn btn-primary">
        <i class="fas fa-arrow-right"></i> العودة للوحة التحكم
    </a>
</div>
@endsection',

    'statistics' => '@extends(\'layouts.main\')

@section(\'title\', \'الإحصائيات\')


@section(\'content\')
<div class="row">
    <div class="col-md-12">
        <h2><i class="fas fa-chart-bar"></i> الإحصائيات</h2>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5>إجمالي الحجوزات</h5>
                <h3>{{ $totalReservations }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5>إجمالي الإيرادات</h5>
                <h3>{{ $totalRevenue }} ريال</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5>الحجوزات الموافق عليها</h5>
                <h3>{{ $approvedCount }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h5>الحجوزات الملغاة</h5>
                <h3>{{ $cancelledCount }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route(\'admin.dashboard\') }}" class="btn btn-primary">
        <i class="fas fa-arrow-right"></i> العودة للوحة التحكم
    </a>
</div>
@endsection'
];

foreach ($views as $name => $content) {
    $path = "{$adminDir}/{$name}.blade.php";
    file_put_contents($path, $content);
    echo "Created: {$path}\n";
}

echo "All admin views created successfully!\n";
