@extends('layouts.main')

@section('title', 'لوحة تحكم الإدارة')

@section('content')

<div class="container-fluid py-4">

    <!-- Title -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">
            <i class="fas fa-tachometer-alt"></i> لوحة تحكم الإدارة
        </h3>
        <span class="badge bg-dark fs-6">Admin Panel</span>
    </div>

    <!-- Statistics -->
    <div class="row g-3">

        <div class="col-6 col-md-3">
            <div class="card shadow border-0 stat-card bg-primary text-white h-100">
                <div class="card-body text-center">
                    <i class="fas fa-money-bill fa-2x mb-2"></i>
                    <h6>إجمالي الإيرادات</h6>
                    <h4 class="fw-bold">{{ $totalRevenue }} درهم</h4>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card shadow border-0 bg-info text-white h-100">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-day fa-2x mb-2"></i>
                    <h6>حجوزات اليوم</h6>
                    <h4 class="fw-bold">{{ $todayReservations }}</h4>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card shadow border-0 bg-warning text-dark h-100">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-month fa-2x mb-2"></i>
                    <h6>حجوزات الشهر</h6>
                    <h4 class="fw-bold">{{ $monthReservations }}</h4>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card shadow border-0 bg-success text-white h-100">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                    <h6>الموافقات</h6>
                    <h4 class="fw-bold">{{ $approvedReservations->count() }}</h4>
                </div>
            </div>
        </div>

    </div>

    <!-- Quick Links -->
    <div class="card shadow border-0 mt-4">
        <div class="card-body text-center">
            <a href="{{ route('admin.reservations') }}" class="btn btn-dark m-2">
                <i class="fas fa-list"></i> جميع الحجوزات
            </a>

            <a href="{{ route('admin.time-slots') }}" class="btn btn-outline-success m-2">
                <i class="fas fa-clock"></i> إدارة المواعيد
            </a>
        </div>
    </div>

    <!-- Tables -->
    <div class="row mt-4 g-4">

        <!-- Pending Payment -->
        <div class="col-md-6">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-warning text-dark fw-bold">
                    <i class="fas fa-clock"></i> في انتظار الدفع
                </div>
                <div class="card-body">

                    @if($pendingReservations->isEmpty())
                        <div class="text-center text-muted">
                            لا توجد حجوزات
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle table-hover">
                                <thead class="table-light">
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
                                        <td>
                                            {{ \Carbon\Carbon::parse($reservation->timeSlot->start_time)->format('H:i') }}
                                        </td>
                                        <td class="fw-bold text-success">
                                            {{ $reservation->total_price }} درهم
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

        <!-- Waiting Approval -->
        <div class="col-md-6">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-info text-white fw-bold">
                    <i class="fas fa-credit-card"></i> في انتظار الموافقة
                </div>
                <div class="card-body">

                    @if($paidReservations->isEmpty())
                        <div class="text-center text-muted">
                            لا توجد حجوزات
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>المستخدم</th>
                                        <th>التاريخ</th>
                                        <th>الوقت</th>
                                        <th>السعر</th>
                                        <th class="text-center">إجراء</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($paidReservations as $reservation)
                                    <tr>
                                        <td>{{ $reservation->user->name }}</td>
                                        <td>{{ $reservation->timeSlot->date }}</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($reservation->timeSlot->start_time)->format('H:i') }}
                                        </td>
                                        <td class="fw-bold text-success">
                                            {{ $reservation->total_price }} درهم
                                        </td>
                                        <td class="text-center">
                                            <form method="POST" action="{{ route('admin.approve', $reservation->id) }}">
                                                @csrf
                                                <button class="btn btn-success btn-sm px-3">
                                                    قبول
                                                </button>
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

</div>

@endsection