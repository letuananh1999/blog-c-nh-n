<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\UserService;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $users = $this->userService->getUsers($status, 10);

        return view('admin.user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate dữ liệu
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'role' => 'required|in:User,Editor,Admin',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'email_verified' => 'nullable|boolean',
            ]);

            // Sử dụng service để tạo user
            $this->userService->create($validated);

            return redirect()->route('admin.users.index')
                ->with('success', 'User thêm thành công');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return view('admin.user.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return view('admin.user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);

            // Validate dữ liệu
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'password' => 'nullable|string|min:8',
                'role' => 'required|in:User,Editor,Admin',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'email_verified' => 'nullable|boolean',
                'version' => 'sometimes|integer|min:0',
            ]);

            // Sử dụng service để cập nhật
            $this->userService->update($user, $validated);

            return redirect()->route('admin.users.index')
                ->with('success', 'User cập nhật thành công');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Toggle user status (khóa/mở khóa)
     */
    public function toggleStatus(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $this->userService->toggleStatus($user);

            $status = $user->status === 'blocked' ? 'Đã khóa' : 'Đã mở khóa';
            return back()->with('success', "User $status thành công");
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);

            // Không cho xóa admin user
            if ($user->role === 'Admin') {
                return back()->with('error', 'Không thể xóa user có vai trò Admin');
            }

            $this->userService->delete($user);

            return redirect()->route('admin.users.index')
                ->with('success', 'User xóa thành công');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}
