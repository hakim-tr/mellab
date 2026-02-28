<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login')->with('error', 'الرجاء تسجيل الدخول أولاً');
        }

        $user = \App\Models\User::find(session('user_id'));
        if (!$user || !$user->isAdmin()) {
            return redirect()->route('home')->with('error', 'ليس لديك صلاحية للوصول لهذه الصفحة');
        }

        return $next($request);
    }
}
