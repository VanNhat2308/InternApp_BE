<?php

namespace App\Http\Controllers;

use App\Models\DiemDanh;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiemDanhController extends Controller
{


    public function checkTodayAttendance(Request $request)
{
    $request->validate([
        'maSV' => 'required|string',
    ]);

    $maSV = $request->maSV;
    $today = now()->toDateString(); // yyyy-mm-dd

    $record = \App\Models\DiemDanh::where('maSV', $maSV)
        ->where('ngay_diem_danh', $today)
        ->first();

    if ($record) {
        return response()->json([
            'status' => 'exists',
            'data' => $record,
        ]);
    } else {
        return response()->json([
            'status' => 'not_found',
            'message' => 'Chưa điểm danh hôm nay.',
        ]);
    }
}



     /**
     * Tạo mới bản ghi điểm danh, đồng thời có thể cập nhật giờ bắt đầu và kết thúc.
     */
    public function storeOrUpdate(Request $request)
    {
        $validated = $request->validate([
            'maSV' => 'required|integer|exists:sinh_viens,maSV',
            'gio_bat_dau' => 'nullable|date_format:H:i:s',
            'gio_ket_thuc' => 'nullable|date_format:H:i:s',
            'trang_thai' => 'nullable|string|in:on_time,late,absent',
        ]);

        $maSV = $validated['maSV'];
        $ngay = Carbon::now()->toDateString();

        $diemDanh = DiemDanh::firstOrNew([
            'maSV' => $maSV,
            'ngay_diem_danh' => $ngay,
        ]);

        // Gán giá trị nếu có
        if (isset($validated['gio_bat_dau'])) {
            $diemDanh->gio_bat_dau = $validated['gio_bat_dau'];
        }

        if (isset($validated['gio_ket_thuc'])) {
            $diemDanh->gio_ket_thuc = $validated['gio_ket_thuc'];
        }

        if (isset($validated['trang_thai'])) {
            $diemDanh->trang_thai = $validated['trang_thai'];
        }

        $diemDanh->save();

        return response()->json([
            'message' => 'Điểm danh đã được tạo hoặc cập nhật thành công',
            'data' => $diemDanh
        ]);
    }

       public function thongKeTuanTheoMaSV($maSV)
    {
        $weeks = [
            'tuanHienTai' => Carbon::now(),
            'tuanTruoc' => Carbon::now()->subWeek()
        ];

        $thuMap = ['mon', 'tue', 'wed', 'thu', 'fri'];
        $result = [];

        foreach ($weeks as $keyWeek => $startDate) {
            $batDau = $startDate->copy()->startOfWeek(); // Monday
            $data = [];

            foreach ($thuMap as $i => $keyThu) {
                $ngay = $batDau->copy()->addDays($i)->toDateString();

                $counts = DB::table('diem_danhs')
                    ->select('trang_thai', DB::raw('count(*) as count'))
                    ->where('maSV', $maSV)
                    ->whereDate('ngay_diem_danh', $ngay)
                    ->groupBy('trang_thai')
                    ->pluck('count', 'trang_thai')
                    ->toArray();

                $onTime = $counts['on_time'] ?? 0;
                $late = $counts['late'] ?? 0;
                $absent = $counts['absent'] ?? 0;

                $data[$keyThu] = [$onTime, $late, $absent];
            }

            $result[$keyWeek] = $data;
        }

        return response()->json($result);
    }
// số buổi đúng giờ/ tổng số buổi
   public function tinhThongKeDiemDanh($maSV)
    {
        // Tổng số buổi có điểm danh (không tính vắng mặt nếu bạn muốn)
        $tongBuoi = DB::table('diem_danhs')
            ->where('maSV', $maSV)
            ->count();

        // Số buổi đúng giờ
        $buoiDungGio = DB::table('diem_danhs')
            ->where('maSV', $maSV)
            ->where('trang_thai', 'on_time')
            ->count();

        return response()->json([
            'maSV' => $maSV,
            'tong_so_buoi' => $tongBuoi,
            'so_buoi_dung_gio' => $buoiDungGio,
        ]);
    }


    /**
     * Tính tổng giờ thực tập của 1 sinh viên theo mã SV.
     */
    public function tinhTongGioThucTap($maSV)
    {
        $tongGio = DB::table('diem_danhs')
            ->where('maSV', $maSV)
            ->whereNotNull('gio_bat_dau')
            ->whereNotNull('gio_ket_thuc')
            ->where('trang_thai', '!=', 'absent')
            ->select(DB::raw('SUM(TIMESTAMPDIFF(SECOND, gio_bat_dau, gio_ket_thuc)) / 3600 as tong_gio'))
            ->value('tong_gio');

        return response()->json([
            'maSV' => $maSV,
            'tong_gio_thuc_tap' => intval($tongGio ?? 0),
        ]);
    }




    // thong ke diem danh
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

  public function danhSachSinhVienDiemDanhHomNay(Request $request)
{
    $homNay = Carbon::today()->toDateString();
    $search = $request->input('search'); // Lấy tham số tìm kiếm

    $query = DiemDanh::with('sinhVien')
        ->whereDate('ngay_diem_danh', $homNay)
        ->when($search, function ($q) use ($search) {
            $q->whereHas('sinhVien', function ($query) use ($search) {
                $query->where('hoTen', 'like', '%' . $search . '%')
                      ->orWhere('maSV', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%');
            });
        });

    $danhSach = $query->paginate(10);

    return response()->json([
        'status' => 'success',
        'ngay' => $homNay,
        'so_luong' => $danhSach->total(),
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

    // Lấy toàn bộ dữ liệu điểm danh của một sinh viên theo mã sinh viên
    public function diemDanhTheoSinhVien(Request $request, $maSV)
    {
        $date = $request->query('date'); // yyyy-mm-dd
        $perPage = $request->query('per_page', 10); // mặc định 10 bản ghi mỗi trang

        $query = DiemDanh::with('sinhVien')->where('maSV', $maSV);

        if ($date) {
            $query->whereDate('ngay_diem_danh', $date);
        }

        $diemDanhList = $query->orderBy('ngay_diem_danh', 'desc')->paginate($perPage);

        if ($diemDanhList->isEmpty()) {
            return response()->json([
                'message' => 'Không có dữ liệu điểm danh vào ngày này cho sinh viên.'
            ], 404);
        }

        return response()->json([
            'maSV' => $maSV,
            'soBuoi' => $diemDanhList->total(),
            'data' => $diemDanhList
        ]);
    }
}
