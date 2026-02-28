@extends('layouts.main')

@section('title', 'تعديل الطلب')

@section('content')

<style>
/* Container عام */
.container { max-width: 900px; }

/* Cards للتوقيت */
.slot-card {
    border: 2px solid #eee;
    border-radius: 12px;
    padding: 15px;
    cursor: pointer;
    transition: 0.3s;
    text-align: center;
    background-color: #f8f9fa;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}
.slot-card:hover {
    border-color: #0d6efd;
    transform: scale(1.05);
}
.slot-card input { display: none; }
.slot-card.selected {
    border-color: #0d6efd;
    background-color: #cfe2ff;
}

/* رؤوس الكارد */
.card-header-custom {
    background-color: #0d6efd;
    color: #fff;
    font-weight: bold;
    font-size: 1rem;
    border-radius: 12px 12px 0 0;
}

/* أزرار */
.btn-success { background-color: #198754; border-color: #198754; }
.btn-primary { background-color: #0d6efd; border-color: #0d6efd; }

/* Responsive mobile layout */
@media (max-width: 767px) {
    .card-body { padding: 12px; }
    .slot-card h5 { font-size: 1rem; margin-bottom:5px; }
    .slot-card p, .slot-card small { font-size: 0.85rem; margin-bottom:3px; }
    .form-control, .btn { font-size: 0.9rem; }
    #timeSlotsContainer .col-12 { padding: 0 5px; }
}
/* Cards للتوقيت */
.slot-card {
    border: 2px solid #eee;
    border-radius: 12px;
    padding: 15px;
    cursor: pointer;
    transition: 0.3s;
    text-align: center;
    background-color: #fff; /* افتراضي: أبيض */
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}
.slot-card.available-slot {
    background-color: #198754; /* أخضر للمتاح */
    color: #fff;
}
.slot-card.selected {
    border-color: #0d6efd;
        background-color: #09ff00ff; /* أخضر للمتاح */

    transform: scale(1.05);
}
</style>

<div class="container mt-3">

    <h3 class="mb-4 text-center"><i class="fas fa-edit"></i> تعديل الطلب</h3>

    {{-- البحث حسب التاريخ --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header card-header-custom">
            البحث حسب التاريخ
        </div>
        <div class="card-body d-flex gap-2 flex-wrap align-items-center">
            <input type="date" id="searchDate" class="form-control" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}">
            <button id="searchBtn" class="btn btn-primary flex-grow-1">🔍 بحث</button>
        </div>
    </div>

    {{-- تعديل الحالة --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header card-header-custom">
            تغيير الحالة
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.reservations.update', $reservation->id) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label>الحالة</label>
                    <select name="status" class="form-control">
                        <option value="pending" {{ $reservation->status == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                        <option value="paid" {{ $reservation->status == 'paid' ? 'selected' : '' }}>مدفوع</option>
                        <option value="approved" {{ $reservation->status == 'approved' ? 'selected' : '' }}>مقبول</option>
                        <option value="cancelled" {{ $reservation->status == 'cancelled' ? 'selected' : '' }}>ملغى</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">حفظ التغييرات</button>
            </form>
        </div>
    </div>

    {{-- تعديل التوقيت --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header card-header-custom">
            تغيير التوقيت
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.reservations.changeTime', $reservation->id) }}">
                @csrf
                @method('PUT')
                <div id="timeSlotsContainer" class="row g-2">
                    {{-- Cards سيتم ملؤها بالـ JS --}}
                </div>
                <button type="submit" class="btn btn-success w-100 mt-3">حفظ التغيير</button>
            </form>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('timeSlotsContainer');
    const searchDate = document.getElementById('searchDate');
    const searchBtn = document.getElementById('searchBtn');

    function loadSlots(date) {
        container.innerHTML = '<div class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> جاري التحميل...</div>';

        fetch(`/get-available-slots?date=${date}`)
            .then(res => res.json())
            .then(data => {
                if (!data.length) {
                    container.innerHTML = '<div class="text-center py-3">لا توجد مواعيد لهذا التاريخ</div>';
                    return;
                }

                let html = '';
                data.forEach(slot => {
                    const isAvailable = slot.is_available;
                    const statusText = isAvailable ? 'متاح' : (slot.reservation_status === 'pending' ? 'قيد الانتظار' :
                                     slot.reservation_status === 'paid' ? 'في انتظار الموافقة' :
                                     slot.reservation_status === 'approved' ? 'موافق عليه' :
                                     slot.reservation_status === 'expired' ? 'منتهي' : 'ملغى');
                    const displayDate = slot.date.split('T')[0];

                    html += `
                        <div class="col-12 col-sm-6 col-md-4 mb-2">
                            <label class="slot-card ${isAvailable ? 'available-slot' : ''}">
                                <input type="radio" name="time_slot_id" value="${slot.id}" ${isAvailable ? 'required' : 'disabled'}>
                                <h5>${displayDate}</h5>
                                <p>${slot.start_time} - ${slot.end_time}</p>
                                <small>${statusText}</small>
                            </label>
                        </div>
                    `;
                });
                container.innerHTML = html;

                document.querySelectorAll('.slot-card.available-slot').forEach(card => {
                    card.addEventListener('click', function() {
                        document.querySelectorAll('.slot-card').forEach(c => c.classList.remove('selected'));
                        this.classList.add('selected');
                        this.querySelector('input').checked = true;
                    });
                });
            })
            .catch(() => container.innerHTML = '<div class="text-center py-3 text-danger">حدث خطأ في تحميل المواعيد</div>');
    }

    loadSlots(searchDate.value);

    searchBtn.addEventListener('click', () => loadSlots(searchDate.value));
});
</script>

@endsection