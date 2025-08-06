<?php

namespace App\Http\Controllers;

use App\Models\Lich;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class LichController extends Controller
{
   // Route: GET /api/schedule/check
public function checkCa(Request $request)
{
    $type = $request->query('type'); // old hoặc new
    $date = $request->query('date');
    $ca = $request->query('ca');
    $maSV = $request->query('maSV');

    $lich = Lich::where('maSV', $maSV)
        ->where('ngay', $date)
        ->where('time', $ca)
        ->first();

    return response()->json([
        'exists' => (bool) $lich,
        'maLich' => $lich?->maLich // hoặc $lich->maLich nếu bạn có cột đó
    ]);
}




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
    // $temp = Lich::where('maSV', '=', $maSV)->get();
    // return response()->json([$maSV,$lichs, $temp], 400);
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
        'ngay' => 'required|date',
        'ca' => 'required|in:8:00-12:00,13:00-17:00',
    ]);
    
    $mapCa = [
        '8:00-12:00' => ['time' => '08:00', 'duration' => 4],
        '13:00-17:00' => ['time' => '13:00', 'duration' => 4],
    ];
    $ngay = Carbon::createFromFormat('Y-m-d', $request->ngay,'Asia/Ho_Chi_Minh');
    // $ngay = Carbon::parse($request->ngay)->startOfDay();
    $today = Carbon::today();
    
    // Không cho thêm lịch quá khứ
    if ($ngay->lt($today)) {
        return response()->json(['message' => 'Không thể thêm lịch vào ngày đã qua!'], 400);
    }
    
    // Kiểm tra trùng lịch
    $exist = Lich::where('maSV', $request->maSV)
    ->where('ngay', '=',$ngay->format('Y-m-d'))
    ->where('time', '=',$mapCa[$request->ca]['time'])
    ->exists();
    
    if ($exist) {
        return response()->json(['message' => 'Lịch bị trùng khung giờ!'], 409);
    }
    // return response()->json([$ngay, $mapCa[$request->ca]['time'], $exist], 409);

    // Tạo lịch
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

public function taoLichChoNhieuSinhVien(Request $request)
{
    $request->validate([
        'maSV' => 'required|array|min:1',
        'maSV.*' => 'exists:sinh_viens,maSV',
        'ngay' => 'required|date',
        'ca' => 'required|in:8:00-12:00,13:00-17:00',
    ]);

    $mapCa = [
        '8:00-12:00' => ['time' => '08:00', 'duration' => 4],
        '13:00-17:00' => ['time' => '13:00', 'duration' => 4],
    ];

    $ngay = Carbon::createFromFormat('Y-m-d', $request->ngay, 'Asia/Ho_Chi_Minh');
    $today = Carbon::today();

    if ($ngay->lt($today)) {
        return response()->json(['message' => 'Không thể thêm lịch vào ngày đã qua!'], 400);
    }

    $caInfo = $mapCa[$request->ca];
    $trungLich = [];

    // Lặp từng sinh viên để kiểm tra trùng
    foreach ($request->maSV as $maSV) {
        $daTrung = Lich::where('maSV', $maSV)
            ->where('ngay', $ngay->format('Y-m-d'))
            ->where('time', $caInfo['time'])
            ->exists();

        if ($daTrung) {
            $sinhVien = \App\Models\SinhVien::where('maSV', $maSV)->first();
            $trungLich[] = $sinhVien->hoTen ?? $maSV;
        }
    }

    // Nếu có sinh viên trùng, trả về lỗi
    if (!empty($trungLich)) {
        return response()->json([
            'message' => 'Lịch bị trùng cho sinh viên: ' . implode(', ', $trungLich),
        ], 409);
    }

    // Nếu không có trùng, tạo lịch cho từng sinh viên
    $createdLich = [];

    foreach ($request->maSV as $maSV) {
        $lich = Lich::create([
            'maLich' => 'LICH' . strtoupper(uniqid()),
            'maSV' => $maSV,
            'ngay' => $ngay->toDateString(),
            'time' => $caInfo['time'],
            'duration' => $caInfo['duration'],
            'noiDung' => 'Lịch được thêm thủ công',
            'trangThai' => 'Chưa học',
        ]);

        $createdLich[] = $lich;
    }

    return response()->json([
        'message' => 'Tạo lịch thành công',
        'data' => $createdLich,
    ]);
}





}
