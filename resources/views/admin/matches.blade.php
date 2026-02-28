@extends('layouts.main')

@section('title', 'إدارة المواعيد')

@section('content')

<style>

/* ===== TABLE DESIGN ===== */
.admin-table {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
}

.admin-table thead {
    background: linear-gradient(45deg, #1e3c72, #2a5298);
    color: white;
}

.admin-table th, .admin-table td {
    padding: 15px;
    text-align: center;
}

.admin-table tbody tr:hover {
    background: #f5f7fa;
    transition: 0.3s;
}

/* ===== STATUS BADGES ===== */
.badge-status {
    padding: 8px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: bold;
}

.status-available { background: #28a745; color: white; }
.status-pending { background: #ffc107; }
.status-paid { background: #17a2b8; color: white; }
.status-approved { background: #007bff; color: white; }
.status-cancelled { background: #dc3545; color: white; }

/* ===== BUTTONS ===== */
.btn-action {
    border-radius: 20px;
    padding: 6px 14px;
    font-size: 13px;
    margin: 2px;
    transition: 0.3s;
}

.btn-approve { background: #28a745; color: white; }
.btn-cancel { background: #dc3545; color: white; }
.btn-reset { background: #6c757d; color: white; }

.btn-action:hover {
    transform: scale(1.05);
    opacity: 0.9;
}

</style>

<div class="container mt-4">

    <h3 class="mb-4"><i class="fas fa-calendar-alt"></i> إدارة المواعيد</h3>

    <div class="table-responsive admin-table">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>التاريخ</th>
                    <th>الوقت</th>
                    <th>المستخدم</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>

                @foreach($reservations as $reservation)
                <tr>
                    <td>{{ $reservation->timeSlot->date }}</td>
                    <td>{{ $reservation->timeSlot->start_time }} - {{ $reservation->timeSlot->end_time }}</td>
                    <td>{{ $reservation->user->name ?? '-' }}</td>

                    <td>
                        @if($reservation->status == 'pending')
                            <span class="badge-status status-pending">قيد الانتظار</span>
                        @elseif($reservation->status == 'paid')
                            <span class="badge-status status-paid">مدفوع</span>
                        @elseif($reservation->status == 'approved')
                            <span class="badge-status status-approved">مقبول</span>
                        @elseif($reservation->status == 'cancelled')
                            <span class="badge-status status-cancelled">ملغى</span>
                        @endif
                    </td>

                    <td>
                        @if($reservation->status == 'paid')
                            <form method="POST" action="{{ route('admin.approve', $reservation->id) }}" class="d-inline">
                                @csrf
                                <button class="btn btn-action btn-approve">قبول</button>
                            </form>
                        @endif

                        @if(in_array($reservation->status, ['pending','paid']))
                            <form method="POST" action="{{ route('admin.cancel', $reservation->id) }}" class="d-inline">
                                @csrf
                                <button class="btn btn-action btn-cancel">إلغاء</button>
                            </form>
                        @endif

                        @if($reservation->status == 'cancelled')
                            <form method="POST" action="{{ route('admin.reset', $reservation->id) }}" class="d-inline">
                                @csrf
                                <button class="btn btn-action btn-reset">إرجاع متاح</button>
                            </form>
                        @endif
                    </td>

                </tr>
                @endforeach

            </tbody>
        </table>
    </div>

</div>

@endsection