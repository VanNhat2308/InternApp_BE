<?php

namespace App\Http\Controllers;

use App\Models\HoSo;
use Illuminate\Http\Request;

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
    $search = $request->input('search');
    $viTri = array_filter(explode(',', $request->input('vi_tri', '')));
    $truong = array_filter(explode(',', $request->input('truong', '')));
    $kyThucTap = $request->input('ky_thuc_tap');

    $query = HoSo::with(['sinhVien.truong']); // eager load sinhVien và truong

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
