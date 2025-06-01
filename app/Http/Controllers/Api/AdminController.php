<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Trả về tất cả admin
    public function index()
    {
        return response()->json(Admin::all());
    }

    // Trả về chi tiết 1 admin theo maAdmin
    public function show($maAdmin)
    {
        $admin = Admin::where('maAdmin', $maAdmin)->first();

        if (!$admin) {
            return response()->json(['message' => 'Admin không tồn tại'], 404);
        }

        return response()->json($admin);
    }
}
