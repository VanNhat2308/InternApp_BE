<?php

namespace App\Http\Controllers;

use App\Models\LoginHistory;
use Illuminate\Http\Request;

class LoginHistoryController extends Controller
{
      // Lấy tất cả lịch sử đăng nhập
    public function index()
    {
        $histories = LoginHistory::orderBy('login_at', 'desc')->take(10)->get();

        return response()->json($histories);
    }




}
