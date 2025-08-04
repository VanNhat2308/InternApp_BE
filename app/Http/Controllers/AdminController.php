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
    $perPage = $request->input('per_page', 10); // Mặc định 10 bản ghi mỗi trang

    $admins = Admin::when($search, function ($query) use ($search) {
        $query->where('hoTen', 'like', "%{$search}%");
    })
    ->select('maAdmin', 'hoTen', 'email')
    ->paginate($perPage);

   return response()->json([
    'data' => $admins
]);

}


}
