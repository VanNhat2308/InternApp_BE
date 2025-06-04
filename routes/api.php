<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DiemDanhController;
use App\Http\Controllers\HoSoController;
use App\Http\Controllers\SinhVienController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;


// sinh vien route
Route::get('/sinhviens/countSV', [SinhVienController::class, 'countSV']);
Route::get('/sinhviens/danh-sach-diem-danh', [SinhVienController::class, 'getAllSinhVienDiemDanh']);
Route::get('/sinhviens/danh-sach-diem-danh/{maSv}', [SinhVienController::class, 'getSinhVienDiemDanh']);
Route::get('/sinhviens/diem-danh-hom-nay', [SinhVienController::class, 'getAllSinhVienDiemDanhHomNay']);



// ho so route
Route::get('/hoso/counths', [HoSoController::class, 'countHS']);


// task route
Route::get('/student/tasks', [TaskController::class, 'index']);
Route::get('/student/tasks/countTask', [TaskController::class, 'countTasks']);
Route::get('/tasks/{id}', [TaskController::class, 'show']);



// diem danh route
Route::get('/diem-danh/so-luong-hom-nay', [DiemDanhController::class, 'soLuongDiemDanhHomNay']);
Route::get('/diem-danh/danh-sach-hom-nay', [DiemDanhController::class, 'danhSachSinhVienDiemDanhHomNay']);
Route::get('/diem-danh/thong-ke-tuan', [DiemDanhController::class, 'thongKeTuanTruocVaHienTai']);



Route::get('/test', function () {
        return response()->json(['message' => '✅ Hello from Laravel 12 API!']);
    });


// auth provider
Route::get('/login/user', [AuthController::class, 'index']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
