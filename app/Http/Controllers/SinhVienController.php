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
    $search = $request->input('search');
    $viTri = array_filter(explode(',', $request->input('vi_tri', '')));
    $truong = array_filter(explode(',', $request->input('truong', '')));
    $kyThucTap = $request->input('ky_thuc_tap');

    $query = SinhVien::with('truong');

    // Tìm kiếm theo tên sinh viên
    if ($search) {
        $query->where('hoTen', 'like', "%{$search}%");
    }

    // Lọc theo nhiều vị trí (gần đúng)
    if (!empty($viTri)) {
        $query->where(function ($q) use ($viTri) {
            foreach ($viTri as $value) {
                $q->orWhere('viTri', 'like', '%' . $value . '%');
            }
        });
    }

    // Lọc theo nhiều trường (gần đúng)
    if (!empty($truong)) {
        $query->whereHas('truong', function ($q) use ($truong) {
            $q->whereIn('tenTruong', array_map('trim', $truong));
        });
    }

    // Lọc theo kỳ thực tập
    if ($kyThucTap) {
        $query->where('ky_thuc_tap', $kyThucTap);
    }

    // Paginate & Trả kết quả
    $sinhViens = $query->paginate($perPage);

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
    // Bước 1: Xác thực dữ liệu
    $validated = $request->validate([
        'maSV'           => 'required|string|unique:sinh_viens,maSV',
        'tenDangNhap'    => 'required|string|unique:sinh_viens,tenDangNhap',
        'password'       => 'required|string|min:6',
        'hoTen'          => 'required|string|max:255',
        'email'          => 'required|email|unique:sinh_viens,email',
        'soDienThoai'    => 'nullable|string|max:15',
        'diaChi'         => 'nullable|string',
        'ngaySinh'       => 'nullable|date',
        'gioiTinh'       => 'nullable|in:Nam,Nữ,Khác',
        'nganh'          => 'nullable|string',
        'duLieuKhuonMat' => 'nullable|string',
        'cV'             => 'nullable|string',
        'soDTGV'         => 'nullable|string',
        'tenGiangVien'   => 'nullable|string',
        'thoiGianTT'     => 'nullable|string',
        'viTri'          => 'nullable|string',
         'maTruong' => 'required|exists:truongs,maTruong'
// hoặc maTruong nếu khác
    ]);

    // Bước 2: Lưu sinh viên mới
    $password = $validated['password'] ?? 'pwd123';
    $sinhVien = SinhVien::create([
        'maSV'           => $validated['maSV'],
        'tenDangNhap'    => $validated['tenDangNhap'],
        'password'       => bcrypt($validated['password']),
        'hoTen'          => $validated['hoTen'],
        'email'          => $validated['email'],
        'soDienThoai'    => $validated['soDienThoai'] ?? null,
        'diaChi'         => $validated['diaChi'] ?? null,
        'ngaySinh'       => $validated['ngaySinh'] ?? null,
        'gioiTinh'       => $validated['gioiTinh'] ?? null,
        'nganh'          => $validated['nganh'] ?? null,
        'duLieuKhuonMat' => $validated['duLieuKhuonMat'] ?? null,
        'cV'             => $validated['cV'] ?? null,
        'soDTGV'         => $validated['soDTGV'] ?? null,
        'tenGiangVien'   => $validated['tenGiangVien'] ?? null,
        'thoiGianTT'     => $validated['thoiGianTT'] ?? null,
        'viTri'          => $validated['viTri'] ?? null,
        'maTruong'       => $validated['maTruong'],
    ]);

    // Bước 3: Trả kết quả
    return response()->json([
        'status' => 'success',
        'message' => 'Thêm sinh viên thành công',
        'data' => $sinhVien
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
