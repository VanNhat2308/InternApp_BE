<?php

namespace App\Http\Controllers;

use App\Models\Lich;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LichController extends Controller
{
public function lichTheoTuan(Request $request)
{
    $weekOffset = (int) $request->input('week', 0);
    $maSV = $request->input('maSV'); // lấy mã sinh viên từ query

    if (!$maSV) {
        return response()->json(['error' => 'Thiếu mã sinh viên'], 400);
    }

    // Tính ngày bắt đầu và kết thúc của tuần tương ứng
    $today = Carbon::today();
    $startOfWeek = $today->copy()->startOfWeek(Carbon::MONDAY)->addWeeks($weekOffset);
    $endOfWeek = $startOfWeek->copy()->endOfWeek(Carbon::SUNDAY);

    // Truy vấn lịch theo sinh viên và trong khoảng ngày của tuần
    $lichs = Lich::where('maSV', $maSV)
                ->whereBetween('ngay', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
                ->get();

    // Chuyển đổi dữ liệu cho frontend
    $events = $lichs->map(function ($lich) {
        $carbonDate = Carbon::parse($lich->ngay);
        return [
            'id' => $lich->maLich,
            'day' => $carbonDate->format('D'),    // Tue
            'thu' => $carbonDate->format('l'),    // Tuesday
            'date' => $carbonDate->day,
            'month' => $carbonDate->month,
            'year' => $carbonDate->year,
            'ngay' => $carbonDate->toDateString(),
            'time' => $lich->time,               // cần có cột time trong DB
            'duration' => $lich->duration,       // cần có cột duration
            'noiDung' => $lich->noiDung,
            'trangThai' => $lich->trangThai,
        ];
    });

    return response()->json($events);
}

    public function xoaTheoId($id)
    {
        $lich = Lich::find($id);

        if (!$lich) {
            return response()->json(['message' => 'Không tìm thấy lịch'], 404);
        }

        $lich->delete();

        return response()->json(['message' => 'Xóa lịch thành công']);
    }


    public function xoaTheoMaSV($maSV)
    {
        $soLuong = Lich::where('maSV', $maSV)->count();

        if ($soLuong === 0) {
            return response()->json(['message' => 'Sinh viên không có lịch để xóa'], 404);
        }

        Lich::where('maSV', $maSV)->delete();

        return response()->json([
            'message' => 'Đã xóa toàn bộ lịch của sinh viên',
            'so_luong' => $soLuong
        ]);
    }

//     public function lichTheoThang(Request $request)
// {
//     $maSV = $request->input('maSV');
//     $month = $request->input('month');
//     $year = $request->input('year');

//     if (!$maSV || !$month || !$year) {
//         return response()->json(['error' => 'Thiếu tham số maSV, month, year'], 400);
//     }

//     $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
//     $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();

//     $lich = Lich::where('maSV', $maSV)
//         ->whereBetween('ngay', [$startDate, $endDate])
//         ->select('maLich', 'ngay', 'time', 'duration')
//         ->get();

//     // Format lại dữ liệu
//     $result = $lich->map(function ($item) {
//         $date = Carbon::parse($item->ngay);
//         return [
//             'id' => $item->maLich,
//             'day' => $date->format('D'), // "Mon", "Tue", etc.
//             'time' => $item->time,
//             'duration' => $item->duration,
//             'week' => $date->weekOfMonth,
//         ];
//     });

//     return response()->json($result);
// }

public function lichTheoThang(Request $request)
{
    $maSV = $request->input('maSV');
    $month = $request->input('month');
    $year = $request->input('year');

    if (!$maSV || !$month || !$year) {
        return response()->json(['error' => 'Thiếu tham số maSV, month, year'], 400);
    }

    $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
    $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();

    $lich = Lich::where('maSV', $maSV)
        ->whereBetween('ngay', [$startDate, $endDate])
        ->select('maLich', 'ngay', 'time', 'duration')
        ->get();

    $result = $lich->map(function ($item) {
        return [
            'id' => $item->maLich,
            'date' => Carbon::parse($item->ngay)->toDateString(), // YYYY-MM-DD
            'time' => $item->time,
            'duration' => $item->duration,
        ];
    });

    return response()->json($result);
}


public function taoLich(Request $request)
{
    $request->validate([
        'maSV' => 'required',
        'thu' => 'required|in:Mon,Tue,Wed,Thu,Fri',
        'ca' => 'required|in:8:00-12:00,13:00-17:00',
        'year' => 'required|integer',
        'month' => 'required|integer',
        'week' => 'required|integer',
    ]);

    $mapCa = [
        '8:00-12:00' => ['time' => '08:00', 'duration' => 4],
        '13:00-17:00' => ['time' => '13:00', 'duration' => 4],
    ];

    $thuToCarbon = [
        'Mon' => Carbon::MONDAY,
        'Tue' => Carbon::TUESDAY,
        'Wed' => Carbon::WEDNESDAY,
        'Thu' => Carbon::THURSDAY,
        'Fri' => Carbon::FRIDAY,
    ];

    $firstDay = Carbon::createFromDate($request->year, $request->month, 1)->startOfMonth();
    $thuNum = $thuToCarbon[$request->thu];

    // Tìm ngày trong tuần và tuần cụ thể
    $ngay = $firstDay->copy()->next($thuNum);
    while ($ngay->weekOfMonth < $request->week) {
        $ngay->addWeek();
    }

    // Kiểm tra trùng lịch
    $exist = Lich::where('maSV', $request->maSV)
        ->where('ngay', $ngay->toDateString())
        ->where('time', $mapCa[$request->ca]['time'])
        ->exists();

    if ($exist) {
        return response()->json([
            'message' => 'Lịch bị trùng khung giờ!',
        ], 409);
    }

    // Tạo lịch mới
    $lich = Lich::create([
        'maLich' => 'LICH' . strtoupper(uniqid()),
        'maSV' => $request->maSV,
        'ngay' => $ngay->toDateString(),
        'time' => $mapCa[$request->ca]['time'],
        'duration' => $mapCa[$request->ca]['duration'],
        'noiDung' => 'Lịch được thêm thủ công',
        'trangThai' => 'Chưa học',
    ]);

    return response()->json([
        'message' => 'Tạo lịch thành công',
        'data' => $lich,
    ]);
}



}
