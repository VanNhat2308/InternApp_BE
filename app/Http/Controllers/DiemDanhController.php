<?php

namespace App\Http\Controllers;

use App\Models\DiemDanh;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiemDanhController extends Controller
{
// 
  public function thongKeDiemDanh($maSV)
    {
        $data = [
            'week' => $this->thongKeTheoKhoang($maSV, Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()),
            'month' => $this->thongKeTheoKhoang($maSV, Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()),
            'semester' => $this->thongKeTheoHocKy($maSV),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    protected function thongKeTheoKhoang($maSV, $startDate, $endDate)
    {
        $counts = DB::table('diem_danhs')
            ->where('maSV', $maSV)
            ->whereBetween('ngay_diem_danh', [$startDate->toDateString(), $endDate->toDateString()])
            ->select('trang_thai', DB::raw('count(*) as total'))
            ->groupBy('trang_thai')
            ->pluck('total', 'trang_thai');

        return [
            [
                'name' => 'Đúng giờ',
                'value' => $counts['on_time'] ?? 0,
                'color' => '#00cc00'
            ],
            [
                'name' => 'Trễ',
                'value' => $counts['late'] ?? 0,
                'color' => '#fbc02d'
            ],
            [
                'name' => 'Nghỉ làm',
                'value' => $counts['absent'] ?? 0,
                'color' => '#f44336'
            ]
        ];
    }

    protected function thongKeTheoHocKy($maSV)
    {
        // Giả sử học kỳ là 6 tháng gần nhất
        $startSemester = Carbon::now()->subMonths(6)->startOfMonth();
        $endSemester = Carbon::now();

        return $this->thongKeTheoKhoang($maSV, $startSemester, $endSemester);
    }

    // 
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

    // Lấy danh sách điểm danh hôm nay, phân trang 15 sinh viên mỗi lần
    $danhSach = DiemDanh::with('sinhVien')
        ->whereDate('ngay_diem_danh', $homNay)
        ->paginate(15); // phân trang

    return response()->json([
        'status' => 'success',
        'ngay' => $homNay,
        'so_luong' => $danhSach->total(),  // tổng tất cả sinh viên điểm danh hôm nay
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
                SUM(CASE WHEN trang_thai = 'on_time' THEN 1 ELSE 0 END) as on_time,
                SUM(CASE WHEN trang_thai = 'late' THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN trang_thai = 'absent' THEN 1 ELSE 0 END) as absent
            ")->first();

        $result['tuanTruoc'][$dayKey] = [
            (int) $countsTruoc->on_time,
            (int) $countsTruoc->late,
            (int) $countsTruoc->absent,
        ];

        // Ngày trong tuần hiện tại
        $dateHienTai = $startTuanHienTai->copy()->addDays($i)->toDateString();

        $countsHienTai = DiemDanh::whereDate('ngay_diem_danh', $dateHienTai)
            ->selectRaw("
                SUM(CASE WHEN trang_thai = 'on_time' THEN 1 ELSE 0 END) as on_time,
                SUM(CASE WHEN trang_thai = 'late' THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN trang_thai = 'absent' THEN 1 ELSE 0 END) as absent
            ")->first();

        $result['tuanHienTai'][$dayKey] = [
            (int) $countsHienTai->on_time,
            (int) $countsHienTai->late,
            (int) $countsHienTai->absent,
        ];
    }

    return response()->json($result);
}

}
