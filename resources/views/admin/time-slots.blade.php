@extends('layouts.main')

@section('title', 'إدارة المواعيد')

@section('content')
<div class="container py-3">

    <!-- Top Menu Buttons -->
    <div class="d-flex flex-wrap justify-content-between mb-3">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary mb-2 flex-grow-1 me-1">
            <i class="fas fa-tachometer-alt"></i> لوحة التحكم
        </a>
        <a href="{{ route('admin.reservations') }}" class="btn btn-info mb-2 flex-grow-1 me-1">
            <i class="fas fa-list"></i> جميع الحجوزات
        </a>
        <a href="{{ route('admin.time-slots') }}" class="btn btn-success mb-2 flex-grow-1 me-1">
            <i class="fas fa-clock"></i> إدارة المواعيد
        </a>
        {{-- <a href="{{ route('admin.create-time-slots') }}" class="btn btn-warning mb-2 flex-grow-1">
            <i class="fas fa-plus-circle"></i> إضافة مواعيد
        </a> --}}
    </div>

    <!-- إدارة إيقاف الحجز -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-ban"></i> إدارة إيقاف الحجز</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.blockSlots') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">نوع الإيقاف</label>
                        <select name="type" class="form-select" required>
                            <option value="hours">إيقاف ساعات محددة</option>
                            <option value="day">إيقاف يوم كامل</option>
                            <option value="month">إيقاف شهر كامل</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">التاريخ</label>
                        <input type="date" name="date" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">من الساعة</label>
                        <input type="time" name="start_time" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">إلى الساعة</label>
                        <input type="time" name="end_time" class="form-control">
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-danger px-4">🚫 تطبيق الإيقاف</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Form بحث وفلترة -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-search"></i> بحث وفلترة المواعيد</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.time-slots') }}" class="row g-2">
                <div class="col-6 col-md-3">
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-6 col-md-3">
                    <select name="status" class="form-select">
                        <option value="">الحالة</option>
                        <option value="available" {{ request('status')=='available'?'selected':'' }}>متاح</option>
                        <option value="booked" {{ request('status')=='booked'?'selected':'' }}>محجوز</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-primary w-100">بحث</button>
                </div>
            </form>
        </div>
    </div>

    <!-- جدول المواعيد -->
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-clock"></i> المواعيد الحالية</h5>
        </div>
        <div class="card-body">
            @if(isset($timeSlots) && $timeSlots->isNotEmpty())
                @foreach($timeSlots as $date => $slots)
                    <h6 class="mt-3 fw-bold">{{ $date }}</h6>
                    <div class="row g-2">
                        @foreach($slots as $slot)
                        <div class="col-6 col-md-2">
                            <div class="card mb-2 {{ $slot->is_available ? 'bg-success' : 'bg-secondary' }} text-white text-center p-2 shadow-sm">
                                <small>{{ $slot->start_time }} - {{ $slot->end_time }}</small>
                                <br>
                                <span class="badge {{ $slot->is_available ? 'bg-light text-dark' : 'bg-dark' }}">
                                    {{ $slot->is_available ? 'متاح' : 'محجوز' }}
                                </span>
                                <div class="mt-1 d-flex flex-column">
                                    @if($slot->is_available)
                                        <form method="POST" action="{{ route('admin.delete-time-slot', $slot->id) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm w-100 mb-1">حذف</button>
                                        </form>
                                    @endif
                                    <button class="btn btn-warning btn-sm w-100" data-bs-toggle="modal" data-bs-target="#editModal{{ $slot->id }}">تعديل</button>
                                </div>
                            </div>

                            <!-- Modal تعديل الموعد -->
                            <div class="modal fade" id="editModal{{ $slot->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form method="POST" action="{{ route('admin.update-time-slot', $slot->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-content rounded shadow">
                                            <div class="modal-header bg-warning text-dark">
                                                <h5 class="modal-title">تعديل الموعد</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-2">
                                                    <label>وقت البداية</label>
                                                    <input type="time" name="start_time" class="form-control" value="{{ $slot->start_time }}" required>
                                                </div>
                                                <div class="mb-2">
                                                    <label>وقت النهاية</label>
                                                    <input type="time" name="end_time" class="form-control" value="{{ $slot->end_time }}" required>
                                                </div>
                                                <div class="mb-2">
                                                    <label>الحالة</label>
                                                    <select name="is_available" class="form-select">
                                                        <option value="1" {{ $slot->is_available?'selected':'' }}>متاح</option>
                                                        <option value="0" {{ !$slot->is_available?'selected':'' }}>محجوز</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-success w-100">حفظ التغييرات</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                        @endforeach
                    </div>
                @endforeach
            @else
                <p class="text-center text-muted">لا توجد مواعيد متاحة</p>
            @endif
        </div>
    </div>
</div>

<!-- Scripts -->
@push('scripts')
<script>
    // لتحديث action ديال form ديال التعديل مباشرة من الزر
    document.querySelectorAll('[data-bs-toggle="modal"]').forEach(btn => {
        btn.addEventListener('click', () => {
            let modalId = btn.getAttribute('data-bs-target');
            let modal = document.querySelector(modalId);
            let form = modal.querySelector('form');
            let slotId = modalId.replace('#editModal','');
            form.action = '/admin/time-slots/' + slotId + '/update';
        });
    });
</script>
@endpush

@endsection