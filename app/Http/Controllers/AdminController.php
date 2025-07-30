<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
     public function store(Request $request)
    {
        $request->validate([
            'hoTen' => 'required|string|max:255',
            'email' => 'required|email|unique:admin,email',
            'password' => 'required|min:6',
        ]);

        $admin = Admin::create([
            'hoTen' => $request->hoTen,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Tạo tài khoản admin thành công.',
            'admin' => $admin,
        ]);
    }
    // Ví dụ cho AdminController
public function index(Request $request)
{
    $search = $request->input('search');

    $admins = Admin::when($search, fn($query) =>
        $query->where('name', 'like', "%$search%")
    )->select('maAdmin', 'hoTen', 'email')->get();

    return response()->json($admins);
}

}
