<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', ' نظام حجز ملعب كرة القدم ملعب توريرت  ')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Tahoma', 'Arial', sans-serif;
            /* background-image: 
            url('/Storage/image/m1.jpeg'); */
             
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        width: 100%;
        height: 100vh;
        }
        .navbar {
            background-color: #2c3e50;

                }
        .card {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .slot-available {
            background-color: #28a745 !important;
            color: white;
        }
        .slot-pending {
            background-color: #ffc107 !important;
            color: black;
        }
        .slot-paid {
            background-color: #0d6efd !important;
            color: white;
        }
        .slot-expired, .slot-cancelled {
            background-color: #dc3545 !important;
            color: white;
        }
        .slot-approved {
            background-color: #198754 !important;
            color: white;
        }
        .time-slot {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .time-slot:hover {
            transform: scale(1.05);
        }
        .time-slot.selected {
            border: 3px solid #000;
            transform: scale(1.05);
        }
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
        }
        .sidebar a {
            color: white;
            padding: 15px 20px;
            display: block;
            text-decoration: none;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #495057;
        }
        .stat-card {
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .footer {
            background-color: #2c3e50;
            color: white;
            padding: 20px 0;
            margin-top: 100px;
        }
        .stat-card {
    border-radius: 15px;
    transition: 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-4px);
}

.card {
    border-radius: 15px;
}
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-futbol"></i>    نظام حجز ملعب توريرت  
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    
                    @if(session('user_id'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}">الرئيسية</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('my.reservations') }}">حجوزاتي</a>
                        </li>
                        @if(session('user_role') === 'admin')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">لوحة التحكم</a>
                        </li>
                        @endif
                    @endif
                </ul>
                <ul class="navbar-nav">
                    @if(session('user_id'))
                        <li class="nav-item">
                            <span class="nav-link">{{ session('user_name') }}</span>
                        </li>
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-light btn-sm">تسجيل الخروج</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">تسجيل الدخول</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">تسجيل جديد</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <footer class="footer text-center">
        <div class="container">
            <p>نظام حجز ملعب  كرة القدم - TAWRIRT MELLAB    </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
