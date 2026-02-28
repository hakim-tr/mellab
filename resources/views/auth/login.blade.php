@extends('layouts.main')

@section('title', 'تسجيل الدخول')

@section('content')

<style>

/* ===== BACKGROUND ===== */
body {
    min-height: 100vh;
}

/* ===== LOGIN CARD ===== */
.login-card {
    background: rgba(0, 0, 0, 1);
    backdrop-filter: blur(15px);
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.4);
    padding: 40px;
    color: white;
    transition: 0.3s;
}

.login-card:hover {
    transform: translateY(-5px);
}

/* ===== TITLE ===== */
.login-title {
    font-weight: bold;
    letter-spacing: 1px;
    margin-bottom: 30px;
}

/* ===== INPUTS ===== */
.form-control {
    background: rgba(255,255,255,0.1);
    border: none;
    border-radius: 12px;
    padding: 12px;
    color: white;
    transition: 0.3s;
}

.form-control:focus {
    background: rgba(255,255,255,0.2);
    box-shadow: 0 0 10px #00c6ff;
    color: white;
}

.form-label {
    font-weight: bold;
}

/* ===== BUTTON ===== */
.btn-login {
    background: linear-gradient(45deg, #00c6ff, #0072ff);
    border: none;
    border-radius: 12px;
    padding: 12px;
    font-weight: bold;
    transition: 0.3s;
}

.btn-login:hover {
    transform: scale(1.05);
    box-shadow: 0 10px 20px rgba(0,198,255,0.4);
}

/* ===== LINK ===== */
.register-link a {
    color: #00c6ff;
    text-decoration: none;
    font-weight: bold;
}

.register-link a:hover {
    text-decoration: underline;
}

/* ===== RESPONSIVE ===== */
@media(max-width:768px){
    .login-card {
        padding: 25px;
    }
}

</style>

<div class="row justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="col-md-5">
        <div class="login-card">
            <div class="text-center">
                <h3 class="login-title">
                    <i class="fas fa-user-circle"></i> تسجيل الدخول
                </h3>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <label for="email" class="form-label">البريد الإلكتروني</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">كلمة المرور</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-login w-100">
                    تسجيل الدخول
                </button>
            </form>

            <hr class="my-4" style="border-color: rgba(255,255,255,0.2);">

            <p class="text-center register-link">
                ليس لديك حساب؟
                <a href="{{ route('register') }}">تسجيل جديد</a>
            </p>
        </div>
    </div>
</div>

@endsection