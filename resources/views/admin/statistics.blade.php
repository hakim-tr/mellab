@extends('layouts.main')

@section('title', 'الإحصائيات')

@section('content')
<h2><i class="fas fa-chart-bar"></i> الإحصائيات</h2>

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
                <h5>الحجوزات المعتمدة</h5>
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

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>الإيرادات الشهرية (آخر 6 أشهر)</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>الشهر</th>
                            <th>الإيرادات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i = 0; $i < count($monthlyLabels); $i++)
                        <tr>
                            <td>{{ $monthlyLabels[$i] }}</td>
                            <td>{{ $monthlyRevenue[$i] }} ريال</td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>الحجوزات اليومية (آخر 7 أيام)</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>اليوم</th>
                            <th>عدد الحجوزات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i = 0; $i < count($dailyLabels); $i++)
                        <tr>
                            <td>{{ $dailyLabels[$i] }}</td>
                            <td>{{ $dailyReservations[$i] }}</td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
