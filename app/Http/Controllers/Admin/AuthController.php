<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    // Hiển thị form đăng nhập
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Xử lý đăng nhập
    public function login(LoginRequest $request)
    {
        // Kiểm tra đăng nhập
        // 🔧 Remember for 7 days instead of 60 days
        $rememberFor7Days = $request->filled('remember') ? 7 * 24 * 60 : false;

        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'Admin' // chỉ cho admin đăng nhập vào trang này
        ], $rememberFor7Days)) {
            $request->session()->regenerate();
            return redirect()->route('admin.index');
        }

        return back()->withErrors([
            'email' => 'Tài khoản hoặc mật khẩu không đúng!',
        ])->onlyInput('email');
    }

    // Đăng xuất
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login.form');
    }
}
