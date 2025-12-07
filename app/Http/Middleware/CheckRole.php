<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$roles)
    {
        // Kiểm tra xem user đã đăng nhập chưa
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized - Not logged in'], 401);
        }

        // Kiểm tra role (không phân biệt chữ hoa/thường)
        $userRole = strtolower(Auth::user()->role);
        $roles = array_map('strtolower', $roles);

        if (! in_array($userRole, $roles)) {
            return response()->json(['message' => 'Unauthorized - Insufficient permissions. Your role: ' . Auth::user()->role], 403);
        }

        return $next($request);
    }
}
