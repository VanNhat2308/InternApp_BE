<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BaoCaoController;
use App\Http\Controllers\DiemDanhController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\HoSoController;
use App\Http\Controllers\LichController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NhatKyController;
use App\Http\Controllers\SinhVienController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;


// sinh vien route
Route::get('/sinhviens/countSV', [SinhVienController::class, 'countSV']);
Route::get('/sinhviens/danh-sach-diem-danh', [SinhVienController::class, 'getAllSinhVienDiemDanh']);
Route::get('/sinhviens/danh-sach-diem-danh/{maSv}', [SinhVienController::class, 'getSinhVienDiemDanh']);
Route::get('/sinhviens/diem-danh-hom-nay', [SinhVienController::class, 'getAllSinhVienDiemDanhHomNay']);
Route::get('/sinhviens/lay-danh-sach-sinh-vien', [SinhVienController::class, 'index']);
Route::get('/sinhviens/{maSV}', [SinhVienController::class, 'getSinhVien']);
Route::post('/sinhviens', [SinhVienController::class, 'store']);
Route::get('/sinhviens/{maSV}', [SinhVienController::class, 'show']);
Route::get('/sinhviens/lay-thong-tin-ho-so/{maSV}', [SinhVienController::class, 'showWithHoSo']);
Route::delete('/sinhviens/{maSV}', [SinhVienController::class, 'destroy']);
Route::put('/sinhviens/{maSV}', [SinhVienController::class, 'update']);





// ho so route
Route::get('/hoso/counths', [HoSoController::class, 'countHS']);
Route::get('/hoso/lay-danh-sach-ho-so', [HoSoController::class, 'index']);
Route::post('/sinhviens/duyet-ho-so/{maSV}', [HoSoController::class, 'duyetHoSo']);



// task route
Route::get('/student/tasks', [TaskController::class, 'index']);
Route::get('/student/tasks/countTask', [TaskController::class, 'countTasks']);
Route::get('/student/tasks/tong-task-sv/{maSV}', [TaskController::class, 'tongSoTaskTheoSinhVien']);
Route::get('/tasks/{id}', [TaskController::class, 'show']);
Route::put('/tasks/diem-so/{id}', [TaskController::class, 'updateDiemSo']);
Route::post('/tasks', [TaskController::class, 'store']);
Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);



// diem danh route
Route::get('/diem-danh/so-luong-hom-nay', [DiemDanhController::class, 'soLuongDiemDanhHomNay']);
Route::get('/diem-danh/danh-sach-hom-nay', [DiemDanhController::class, 'danhSachSinhVienDiemDanhHomNay']);
Route::get('/diem-danh/thong-ke-tuan', [DiemDanhController::class, 'thongKeTuanTruocVaHienTai']);
Route::get('/diem-danh/thong-ke-tuan-sv/{maSV}', [DiemDanhController::class, 'thongKeTuanTheoMaSV']);
Route::get('/diem-danh/thong-ke/{maSV}', [DiemDanhController::class, 'thongKeDiemDanh']);
Route::get('/diem-danh/sinh-vien/{maSV}', [DiemDanhController::class, 'diemDanhTheoSinhVien']);
Route::get('/diem-danh/tong-gio-thuc-tap/{maSV}', [DiemDanhController::class, 'tinhTongGioThucTap']);
Route::get('/diem-danh/thong-ke-chuyen-can/{maSV}', [DiemDanhController::class, 'tinhThongKeDiemDanh']);
Route::post('/diem-danh/check-today', [DiemDanhController::class, 'checkTodayAttendance']);
Route::post('/diem-danh/store-or-update', [DiemDanhController::class, 'storeOrUpdate']);


Route::get('/test', function () {
        return response()->json(['message' => '✅ Hello from Laravel 12 API!']);
    });


// auth provider
Route::post('/login/admin', [AuthController::class, 'loginAdmin']);
Route::post('/login/sinhvien', [AuthController::class, 'loginSinhvien']);
Route::post('/logout', [AuthController::class, 'logout']);



// file upload
Route::post('/upload', [FileUploadController::class, 'upload']);


// bao cao
Route::get('/bao-cao', [BaoCaoController::class, 'danhSachBaoCao']);
Route::get('/bao-cao/{maBC}', [BaoCaoController::class, 'chiTietBaoCao']);
Route::delete('/bao-cao/{maBC}', [BaoCaoController::class, 'xoaBaoCao']);

// lich
Route::get('/lich/theo-tuan', [LichController::class, 'LichTheoTuan']);
Route::get('/lich/theo-thang', [LichController::class, 'lichTheoThang']);
Route::post('/lich', [LichController::class, 'taoLich']);
Route::delete('/lich/{id}', [LichController::class, 'xoaTheoId']);
Route::delete('/lich/sinhvien/{maSV}', [LichController::class, 'xoaTheoMaSV']);


//message

Route::get('/messages/feedback-panel', [MessageController::class, 'feedbackList']);
Route::get('/messages/conversation/{id}', [MessageController::class, 'getMessages']);


// pusher
Route::post('/messages', [MessageController::class, 'store']);
Route::get('/conversations/{id}/messages', [MessageController::class, 'getMessages']);


// Nhat ky
Route::get('/nhat-ky/details/{maSV}', [NhatKyController::class, 'index']);
Route::post('/nhat-ky/store', [NhatKyController::class, 'store']);
Route::post('/chi-tiet-nhat-ky/store', [NhatKyController::class, 'storeDetail']);
Route::get('/nhat-ky/list/{maSV}', [NhatKyController::class, 'listDiary']);
