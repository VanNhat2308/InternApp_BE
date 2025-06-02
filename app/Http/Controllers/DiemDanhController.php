<?php

namespace App\Http\Controllers;

use App\Models\DiemDanh;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DiemDanhController extends Controller
{
    
public function soLuongDiemDanhHomNay()
{
    $homNay = Carbon::today()->toDateString();

    $soLuong = DiemDanh::where('ngay_diem_danh', $homNay)->distinct('maSV')->count('maSV');

    return response()->json([
        'status' => 'success',
        'so_luong_sinh_vien_diem_danh' => $soLuong,
        'ngay' => $homNay
    ]);
}

public function danhSachSinhVienDiemDanhHomNay()
{
    $homNay = Carbon::today()->toDateString();

    // Lấy danh sách điểm danh hôm nay kèm thông tin sinh viên
    $danhSach = DiemDanh::with('sinhVien')
        ->whereDate('ngay_diem_danh', $homNay)
        ->get();

    return response()->json([
        'status' => 'success',
        'ngay' => $homNay,
        'so_luong' => $danhSach->count(),
        'data' => $danhSach
    ]);
}
public function thongKeTuanTruocVaHienTai()
{
    $days = ['mon', 'tue', 'wed', 'thu', 'fri'];

    // Tuần trước (từ thứ 2 đến thứ 6)
    $startTuanTruoc = Carbon::today()->subWeek()->startOfWeek(Carbon::MONDAY);

    // Tuần hiện tại (từ thứ 2 đến thứ 6)
    $startTuanHienTai = Carbon::today()->startOfWeek(Carbon::MONDAY);

    $result = [
        'tuanTruoc' => [],
        'tuanHienTai' => []
    ];

    foreach ($days as $i => $dayKey) {
        // Ngày trong tuần trước
        $dateTruoc = $startTuanTruoc->copy()->addDays($i)->toDateString();

        $countsTruoc = DiemDanh::whereDate('ngay_diem_danh', $dateTruoc)
            ->selectRaw("
                SUM(CASE WHEN trang_thai = 'co_mat' THEN 1 ELSE 0 END) as co_mat,
                SUM(CASE WHEN trang_thai = 'muon' THEN 1 ELSE 0 END) as muon,
                SUM(CASE WHEN trang_thai = 'vang' THEN 1 ELSE 0 END) as vang
            ")->first();

        $result['tuanTruoc'][$dayKey] = [
            (int) $countsTruoc->co_mat,
            (int) $countsTruoc->muon,
            (int) $countsTruoc->vang,
        ];

        // Ngày trong tuần hiện tại
        $dateHienTai = $startTuanHienTai->copy()->addDays($i)->toDateString();

        $countsHienTai = DiemDanh::whereDate('ngay_diem_danh', $dateHienTai)
            ->selectRaw("
                SUM(CASE WHEN trang_thai = 'co_mat' THEN 1 ELSE 0 END) as co_mat,
                SUM(CASE WHEN trang_thai = 'muon' THEN 1 ELSE 0 END) as muon,
                SUM(CASE WHEN trang_thai = 'vang' THEN 1 ELSE 0 END) as vang
            ")->first();

        $result['tuanHienTai'][$dayKey] = [
            (int) $countsHienTai->co_mat,
            (int) $countsHienTai->muon,
            (int) $countsHienTai->vang,
        ];
    }

    return response()->json($result);
}

}
