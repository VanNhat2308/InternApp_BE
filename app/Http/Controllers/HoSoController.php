<?php

namespace App\Http\Controllers;

use App\Models\HoSo;
use App\Models\SinhVien;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class HoSoController extends Controller
{
    
     // GET /api/hoso/counths
        public function countHS()
        {
            $countHs = HoSo::count();
            return response()->json([
                'status' => 'success',
                'total_hs' => $countHs]
            );
        }

public function index(Request $request)
{
    $perPage = $request->input('per_page', 10);
    $status = $request->input('status');
    $search = $request->input('search');
    $viTri = array_filter(explode(',', $request->input('vi_tri', '')));
    $truong = array_filter(explode(',', $request->input('truong', '')));
    $kyThucTap = $request->input('ky_thuc_tap');

    $query = HoSo::with(['sinhVien.truong'])
    ->where('trangThai', $status)
    ->orderBy('ngayNop', 'desc'); 


    // Tìm kiếm theo tên sinh viên
    if ($search) {
        $query->whereHas('sinhVien', function ($q) use ($search) {
            $q->where('hoTen', 'like', "%{$search}%");
        });
    }

    // Lọc theo nhiều vị trí (gần đúng)
    if (!empty($viTri)) {
        $query->whereHas('sinhVien', function ($q) use ($viTri) {
            $q->where(function ($q2) use ($viTri) {
                foreach ($viTri as $value) {
                    $q2->orWhere('viTri', 'like', '%' . $value . '%');
                }
            });
        });
    }

    // Lọc theo nhiều trường (gần đúng)
    if (!empty($truong)) {
        $query->whereHas('sinhVien.truong', function ($q) use ($truong) {
            $q->whereIn('tenTruong', array_map('trim', $truong));
        });
    }

    // Lọc theo kỳ thực tập
    if ($kyThucTap) {
        $query->whereHas('sinhVien', function ($q) use ($kyThucTap) {
            $q->where('ky_thuc_tap', $kyThucTap);
        });
    }

    // Paginate & Trả kết quả
    $hosos = $query->paginate($perPage);

    return response()->json([
        'status' => 'success',
        'data' => $hosos
    ]);
}



public function store(Request $request)
{
    $request->validate([
        'maSV' => 'required|exists:sinh_viens,maSV|unique:ho_sos,maSV',
        'ngayNop' => 'required|date',
    ]);

    $maHS = 'HS_' . Str::random(8); // VD: HS_ab12cd34

    $hoSo = HoSo::create([
        'maHS' => $maHS,
        'maSV' => $request->maSV,
        'ngayNop' => $request->ngayNop,
        'trangThai' => 'Chờ duyệt',
    ]);

    return response()->json([
        'message' => 'Tạo hồ sơ thành công',
        'data' => $hoSo,
    ], 201);
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
    public function duyetHoSo(Request $request, string $maSV)
{  
    $status = $request->input('status');
    // Tìm sinh viên
    $sinhVien = SinhVien::where('maSV', $maSV)->first();

    if (!$sinhVien) {
        return response()->json([
            'status' => 'error',
            'message' => 'Không tìm thấy sinh viên với mã: ' . $maSV
        ], 404);
    }

    // Tìm hồ sơ của sinh viên
    $hoSo = HoSo::where('maSV', $maSV)->first();

    if (!$hoSo) {
        return response()->json([
            'status' => 'error',
            'message' => 'Không tìm thấy hồ sơ của sinh viên với mã: ' . $maSV
        ], 404);
    }

    // Cập nhật trạng thái
    $hoSo->trangThai = $status;
    $hoSo->save();

    return response()->json([
        'status' => 'success',
        'message' => 'Hồ sơ đã được duyệt thành công',
        'data' => $hoSo
    ]);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
