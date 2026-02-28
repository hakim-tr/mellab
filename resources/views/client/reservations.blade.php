@extends('layouts.main')

@section('title', 'طلباتي')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4 text-center">طلباتك</h3>

    @if($reservations->isEmpty())
        <p class="text-center">لم تقم بأي حجز بعد.</p>
    @else
        <div class="row">
            @foreach($reservations as $res)
            <div class="col-md-6 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5>{{ $res->field->name }}</h5>
                      <p>
<strong>التاريخ:</strong> 
{{ \Carbon\Carbon::parse($res->timeSlot->date)->format('d-m-Y') }}
</p>

<p>
<strong>الوقت:</strong> 
{{ \Carbon\Carbon::parse($res->timeSlot->start_time)->format('H:i') }}
 -
{{ \Carbon\Carbon::parse($res->timeSlot->end_time)->format('H:i') }}
</p>

<p>
<strong>السعر:</strong> 
{{ number_format($res->total_price, 2) }} درهم
</p>   <p>
                            <strong>الحالة:</strong>
                            @if($res->status == 'pending')
                                <span class="badge bg-warning">قيد الانتظار</span>
                            @elseif($res->status == 'paid')
                                <span class="badge bg-info">في انتظار الموافقة</span>
                            @elseif($res->status == 'approved')
                                <span class="badge bg-success">موافق عليه</span>
                            @elseif($res->status == 'cancelled')
                                <span class="badge bg-secondary">ملغى</span>
                            @elseif($res->status == 'expired')
                                <span class="badge bg-danger">منتهي</span>
                            @endif
                        </p>

                        {{-- زر تحميل PDF يظهر فقط إذا تم الدفع أو مقبول --}}
                        @if(in_array($res->status, ['paid', 'approved']))
                            <a href="{{ route('reservation.pdf', $res->id) }}" class="btn btn-success mt-2 w-100">
                                تحميل PDF
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection