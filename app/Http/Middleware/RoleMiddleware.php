<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'กรุณาเข้าสู่ระบบก่อนใช้งาน');
        }

        $user = Auth::user();

        // If no roles specified, just allow authenticated user
        if (empty($roles)) {
            return $next($request);
        }

        $userRole = strtolower($user->role ?? 'user');
        $allowedRoles = array_map('strtolower', $roles);

        if (!in_array($userRole, $allowedRoles, true)) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงส่วนนี้ (เฉพาะผู้ใช้ระดับ ' . implode(', ', $roles) . ' เท่านั้น)');
        }

        return $next($request);
    }
}

