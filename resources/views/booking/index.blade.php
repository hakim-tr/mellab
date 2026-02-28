@extends('layouts.main')

@section('title', 'حجز الملعب')

@section('content')

@section('styles')
<style>

/* ===== GENERAL ===== */
body {
    background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
}

.card {
    border: none;
    border-radius: 18px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    overflow: hidden;
    transition: 0.3s;
}

.card:hover {
    transform: translateY(-5px);
}

/* ===== HEADERS ===== */
.card-header {
    background: linear-gradient(45deg, #1e3c72, #2a5298);
    color: white;
    font-weight: bold;
    letter-spacing: 1px;
    border: none;
}

.card-header h4,
.card-header h5 {
    margin: 0;
}

/* ===== DATE PICKER ===== */
#datePicker {
    border-radius: 12px;
    padding: 12px;
    font-size: 16px;
    border: 2px solid #2a5298;
    transition: 0.3s;
}

#datePicker:focus {
    border-color: #00c6ff;
    box-shadow: 0 0 10px rgba(0,198,255,0.5);
}

/* ===== TIME SLOTS ===== */
.time-slot {
    border-radius: 15px;
    transition: 0.3s;
    border: none;
    color: white;
    font-weight: bold;
}

.time-slot h6 {
    font-size: 15px;
    margin-bottom: 5px;
}

.available-slot {
    background: linear-gradient(45deg, #00b09b, #96c93d);
}

.available-slot:hover {
    transform: scale(1.05);
    box-shadow: 0 10px 20px rgba(0,0,0,0.3);
}

.slot-pending {
    background: linear-gradient(45deg, #f7971e, #ffd200);
}

.slot-paid {
    background: linear-gradient(45deg, #36d1dc, #5b86e5);
}

.slot-approved {
    background: linear-gradient(45deg, #11998e, #38ef7d);
}

.slot-expired {
    background: linear-gradient(45deg, #cb2d3e, #ef473a);
}

.slot-cancelled {
    background: linear-gradient(45deg, #757f9a, #d7dde8);
    color: #333;
}

/* ===== BADGES ===== */
.badge {
    font-size: 14px;
    border-radius: 20px;
    font-weight: bold;
}

/* ===== MODAL ===== */
.modal-content {
    border-radius: 20px;
    border: none;
}

.modal-header {
    background: linear-gradient(45deg, #1e3c72, #2a5298);
    color: white;
}

.modal-footer .btn-primary {
    background: linear-gradient(45deg, #00b09b, #96c93d);
    border: none;
}

.modal-footer .btn-primary:hover {
    opacity: 0.9;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .time-slot h6 {
        font-size: 13px;
    }
}

</style>
@endsection

<div class="row">
    
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4><i class="fas fa-futbol"></i> {{ $field->name }}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong><i class="fas fa-map-marker-alt"></i> الموقع:</strong> {{ $field->location }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong><i class="fas fa-money-bill"></i> السعر:</strong> {{ $field->price_per_hour }} درهم/ساعة</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>اختر التاريخ</h5>
            </div>
            <div class="card-body">
                <input type="date" id="datePicker" class="form-control" 
                       min="{{ date('Y-m-d') }}" 
                       value="{{ date('Y-m-d') }}">
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="row mb-3">
    <div class="col-md-12 text-end">
        <a href="{{ route('client.reservations') }}" class="btn btn-primary">
            <i class="fas fa-list"></i> عرض طلباتي
        </a>
    </div>
</div>
            <div class="card-header">
                <h5>المواعيد المتاحة</h5>
            </div>
            <div class="card-body">
                <div id="timeSlotsContainer">
                    <div class="text-center">
                        <p>الرجاء اختيار التاريخ لعرض المواعيد</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>LEGEND</h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-3">
                    <span class="badge slot-available p-2">متاحة</span>
                    <span class="badge slot-pending p-2">قيد الانتظار</span>
                    <span class="badge slot-paid p-2">في انتظار الموافقة</span>
                    <span class="badge slot-expired p-2">منتهي الصلاحية</span>
                    <span class="badge slot-approved p-2">موافق عليه</span>
                    <span class="badge slot-cancelled p-2">ملغى</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reservation Modal -->
<div class="modal fade" id="reservationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تأكيد الحجز</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من حجز هذا الموعد؟</p>
                <p><strong>التاريخ:</strong> <span id="modalDate"></span></p>
                <p><strong>الوقت:</strong> <span id="modalTime"></span></p>
                <p><strong>السعر:</strong> {{ $field->price_per_hour }} درهم</p>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle"></i> يجب الدفع خلال 24 ساعة أو سيتم إلغاء الحجز تلقائياً
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form method="POST" action="{{ route('reserve') }}">
                    @csrf
                    <input type="hidden" name="time_slot_id" id="timeSlotId">
                    <button type="submit" class="btn btn-primary">تأكيد الحجز</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const datePicker = document.getElementById('datePicker');
    const timeSlotsContainer = document.getElementById('timeSlotsContainer');
    let selectedSlotId = null;

    // Load time slots when date changes
    datePicker.addEventListener('change', function() {
        loadTimeSlots(this.value);
    });

    // Load time slots for today on page load
    loadTimeSlots(datePicker.value);

    function loadTimeSlots(date) {
        timeSlotsContainer.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> جاري التحميل...</div>';

        fetch(`/get-available-slots?date=${date}`)
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    timeSlotsContainer.innerHTML = '<div class="text-center"><p>لا توجد مواعيد لهذا التاريخ</p></div>';
                    return;
                }

                let html = '<div class="row">';
                data.forEach(slot => {
                    let statusClass = '';
                    let statusText = '';
                    let isAvailable = false;
                    let status = '';

                    if (slot.is_available) {
                        statusClass = 'slot-available';
                        statusText = 'متاح';
                        isAvailable = true;
                        status = 'available';
                    } else {
                        // Check reservation status
                        if (slot.reservation_status === 'pending') {
                            statusClass = 'slot-pending';
                            statusText = 'قيد الانتظار';
                            status = 'pending';
                        } else if (slot.reservation_status === 'paid') {
                            statusClass = 'slot-paid';
                            statusText = 'في انتظار الموافقة';
                            status = 'paid';
                        } else if (slot.reservation_status === 'approved') {
                            statusClass = 'slot-approved';
                            statusText = 'موافق عليه';
                            status = 'approved';
                        } else if (slot.reservation_status === 'expired') {
                            statusClass = 'slot-expired';
                            statusText = 'منتهي';
                            status = 'expired';
                        } else if (slot.reservation_status === 'cancelled') {
                            statusClass = 'slot-cancelled';
                            statusText = 'ملغى';
                            status = 'cancelled';
                        }
                    }

                    html += `
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card time-slot ${statusClass} ${isAvailable ? 'available-slot' : ''}" 
                                 data-id="${slot.id}" 
                                 data-date="${slot.date}"
                                 data-start="${slot.start_time}"
                                 data-end="${slot.end_time}"
                                 ${isAvailable ? 'data-bs-toggle="modal" data-bs-target="#reservationModal"' : ''}
                                 style="cursor: ${isAvailable ? 'pointer' : 'not-allowed'}; opacity: ${isAvailable ? '1' : '0.7'}">
                                <div class="card-body text-center">
                                    <h6>${slot.start_time} - ${slot.end_time}</h6>
                                    <small>${statusText}</small>
                                </div>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                timeSlotsContainer.innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                timeSlotsContainer.innerHTML = '<div class="text-center"><p>حدث خطأ في تحميل المواعيد</p></div>';
            });
    }

    // Handle modal content when slot is clicked
    document.addEventListener('click', function(e) {
        const slot = e.target.closest('.available-slot');
        if (slot) {
            document.getElementById('timeSlotId').value = slot.dataset.id;
            document.getElementById('modalDate').textContent = slot.dataset.date;
            document.getElementById('modalTime').textContent = slot.dataset.start + ' - ' + slot.dataset.end;
        }
    });
});
</script>
@endsection
