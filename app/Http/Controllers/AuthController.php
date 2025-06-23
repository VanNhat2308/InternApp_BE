<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Đăng nhập cho sinh viên
     */
    public function loginSinhvien(Request $request)
    {
        return $this->loginWithGuard($request, 'api_sinhvien', 'sinhvien');
    }

    /**
     * Đăng nhập cho admin
     */
    public function loginAdmin(Request $request)
    {
        return $this->loginWithGuard($request, 'api_admin', 'admin');
    }

    /**
     * Hàm xử lý chung cho đăng nhập
     */
private function loginWithGuard(Request $request, $guard, $role)
{
    // 1. Định nghĩa rule
    $rules = [
        'password' => 'required|string|min:6',
    ];

    if ($role === 'sinhvien') {
        $rules['tenDangNhap'] = 'required|string';
    } else {
        $rules['email'] = 'required|email';
    }

    // 2. Validate đầu vào
    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'step' => 'validation',
            'message' => $validator->errors()
        ], 422);
    }

    // 3. Lấy credentials
    $credentials = ($role === 'sinhvien')
        ? $request->only('tenDangNhap', 'password')
        : $request->only('email', 'password');

    // 4. Debug xem credentials là gì
    Log::info("Trying to login [$role] with credentials:", $credentials);

    // 5. Tìm user thủ công trước để kiểm tra password đúng không
    if ($role === 'sinhvien') {
        $user = \App\Models\SinhVien::where('tenDangNhap', $credentials['tenDangNhap'])->first();
    } else {
        $user = \App\Models\Admin::where('email', $credentials['email'])->first();
    }

    if (!$user) {
        return response()->json([
            'status' => 'error',
            'step' => 'user_lookup',
            'message' => 'Không tìm thấy người dùng',
        ], 404);
    }

    // 6. Kiểm tra mật khẩu có khớp không
    if (!\Illuminate\Support\Facades\Hash::check($credentials['password'], $user->getAuthPassword())) {
        return response()->json([
            'status' => 'error',
            'step' => 'password_check',
            'message' => 'Mật khẩu không chính xác',
        ], 401);
    }

    // 7. Cuối cùng attempt với guard
    if (!$token = Auth::guard($guard)->attempt($credentials)) {
        return response()->json([
            'status' => 'error',
            'step' => 'jwt_auth',
            'message' => 'JWT attempt failed. Có thể do guard hoặc model chưa đúng.',
        ], 401);
    }

    return response()->json([
        'status' => 'success',
        'token' => $token,
        'role' => $role,
        'user' => Auth::guard($guard)->user()
    ], 200);
}



    /**
     * Đăng xuất cho cả admin và sinh viên
     */
    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully logged out'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to logout'
            ], 500);
        }
    }
}
