<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\SinhVien;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class PasswordResetController extends Controller
{
    // Gửi OTP
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Tìm email trong Admin hoặc SinhVien
        $user = Admin::where('email', $request->email)->first();
        $role = 'admin';
        if (!$user) {
            $user = SinhVien::where('email', $request->email)->first();
            $role = 'sinhvien';
        }

        if (!$user) {
            return response()->json(['message' => 'Email không tồn tại!'], 404);
        }

        // Tạo OTP
        $otp = rand(100000, 999999);

        // Lưu vào bảng password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $otp,
                'created_at' => Carbon::now(),
                'role' => $role
            ]
        );

        // Gửi email OTP (cần cấu hình mail)
        Mail::raw("Mã OTP đặt lại mật khẩu của bạn là: $otp", function ($message) use ($request) {
            $message->to($request->email)->subject('Mã OTP đặt lại mật khẩu');
        });

        return response()->json(['message' => 'OTP đã được gửi']);
    }

    // Xác thực OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required'
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->otp)
            ->first();

        if (!$record) {
            return response()->json(['message' => 'OTP không hợp lệ!'], 400);
        }

        // Kiểm tra hết hạn (15 phút)
        if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            return response()->json(['message' => 'OTP đã hết hạn!'], 400);
        }

        return response()->json(['message' => 'OTP hợp lệ']);
    }

     public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'otp'      => 'required|string',
            'password' => 'required|min:6'
        ]);

        $otpRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->otp)
            ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'OTP không hợp lệ'], 400);
        }

        // Xác định user thuộc bảng nào
        $user = Admin::where('email', $request->email)->first();
        if (!$user) {
            $user = SinhVien::where('email', $request->email)->first();
        }

        if (!$user) {
            return response()->json(['message' => 'Không tìm thấy tài khoản'], 404);
        }

        // Cập nhật mật khẩu
        $user->password = Hash::make($request->password);
        $user->save();

        // Xoá OTP để tránh reuse
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Đổi mật khẩu thành công']);
    }

public function changePassword(Request $request)
{
    $request->validate([
        'user_id' => 'required|integer',
        'role' => 'required|in:admin,sinhvien',
        'current_password' => 'required|string',
        'new_password' => 'required|string|min:8|confirmed',
    ]);

    // Xác định model theo role
    if ($request->role === 'admin') {
        $user = \App\Models\Admin::find($request->user_id);
    } else {
        $user = \App\Models\SinhVien::find($request->user_id);
    }

    // Nếu không tìm thấy user
    if (!$user) {
        return response()->json(['message' => 'Không tìm thấy người dùng'], 404);
    }
        // Kiểm tra mật khẩu hiện tại
    if (!Hash::check($request->current_password, $user->password)) {
        return response()->json(['message' => 'Mật khẩu hiện tại không đúng'], 400);
    }


    // Cập nhật mật khẩu
    $user->password = Hash::make($request->new_password);
    $user->save();

    return response()->json([
        'message' => 'Đổi mật khẩu thành công',
        'role' => $request->role
    ]);
}




    
}
