<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SinhVien;
use Carbon\Carbon;

class SinhVienController extends Controller
{
      // GET /api/sinhviens/countSV
        public function countSV()
        {
            $countSv = SinhVien::count();
            return response()->json([
                'status' => 'success',
                'total_sv' => $countSv]
            );
        }

        //get /api/sinhviens/{maSv}
public function getAllSinhVienDiemDanh()
{
    $danhSach = SinhVien::with('diemDanhs')->get();

    return response()->json($danhSach);
}



    public function getSinhVienDiemDanh($maSV)
{
    $sinhVien = SinhVien::with('diemDanhs')->find($maSV);

    if (!$sinhVien) {
        return response()->json(['message' => 'Không tìm thấy sinh viên'], 404);
    }

    return response()->json($sinhVien);
}


public function getAllSinhVienDiemDanhHomNay()
{
    $today = Carbon::today()->toDateString();

    // Lấy tất cả sinh viên, chỉ load điểm danh trong ngày hôm nay
    $danhSach = SinhVien::with(['diemDanhs' => function ($query) use ($today) {
        $query->whereDate('ngay_diem_danh', $today);
    }])->get();

    return response()->json($danhSach);
}




public function index(Request $request)
{
    $perPage = $request->input('per_page', 10);

    // Lấy danh sách sinh viên + thông tin trường kèm theo
    $sinhViens = SinhVien::with('truong')->paginate($perPage);

    return response()->json([
        'status' => 'success',
        'data' => $sinhViens
    ]);
}



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
