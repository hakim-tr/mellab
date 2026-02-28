<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login')->with('error', 'الرجاء تسجيل الدخول أولاً');
        }

        $user = \App\Models\User::find(session('user_id'));
        if (!$user) {
            session()->forget('user_id');
            return redirect()->route('login')->with('error', 'الجلسة غير صالحة، الرجاء تسجيل الدخول مرة أخرى');
        }

        return $next($request);
    }
}
