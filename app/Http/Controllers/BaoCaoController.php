<?php

namespace App\Http\Controllers;

use App\Models\BaoCao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BaoCaoController extends Controller
{
public function BaoCaoTheoMa(Request $request)
{
    $maSV = $request->query('maSV');
    $date = $request->query('date'); // Format: YYYY-MM-DD

    $query = BaoCao::query();

    if ($maSV) {
        $query->where('maSV', $maSV);
    }

    if ($date) {
        $query->whereDate('ngayTao', $date);
    }

    $reports = $query->get();

    return response()->json([
        'status' => true,
        'data' => $reports
    ]);
}

   public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'loai' => 'required|string|max:50',
            'ngayTao' => 'required|date',
            'noiDung' => 'required|string',
            'tepDinhKem' => 'nullable|string',
            'maSV' => 'required|integer|exists:sinh_viens,maSV', // điều chỉnh nếu tên bảng khác
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $baoCao = BaoCao::create([
            'loai' => $request->loai,
            'ngayTao' => $request->ngayTao,
            'noiDung' => $request->noiDung,
            'tepDinhKem' => $request->tepDinhKem,
            'maSV' => $request->maSV,
        ]);

        return response()->json([
            'message' => 'Tạo báo cáo thành công',
            'data' => $baoCao
        ], 201);
    }

public function danhSachBaoCao(Request $request)
{
    $perPage = $request->input('per_page', 10);
    $search = $request->input('search');
    $viTri = array_filter(explode(',', $request->input('vi_tri', '')));
    $truong = array_filter(explode(',', $request->input('truong', '')));
    $date = $request->input('date');

    $query = BaoCao::with(['sinhVien.truong']) 
        ->orderBy('ngayTao', 'asc');

    // Filter theo sinh viên liên quan
    $query->whereHas('sinhVien', function ($q) use ($search, $viTri, $truong) {
        // Tìm kiếm tên sinh viên
        if ($search) {
            $q->where('hoTen', 'like', "%{$search}%");
        }

        // Lọc theo vị trí
        if (!empty($viTri)) {
            $q->where(function ($subQ) use ($viTri) {
                foreach ($viTri as $value) {
                    $subQ->orWhere('viTri', 'like', '%' . $value . '%');
                }
            });
        }

        // Lọc theo trường
        if (!empty($truong)) {
            $q->whereHas('truong', function ($subQ) use ($truong) {
                $subQ->whereIn('tenTruong', array_map('trim', $truong));
            });
        }
    });

    // ✅ Thêm điều kiện lọc theo ngày
    if ($date) {
        $query->whereDate('ngayTao', $date);
    }

    $baoCaos = $query->paginate($perPage);

    return response()->json([
        'status' => 'success',
        'tongSoBaoCao' => $baoCaos->total(),
        'baoCaos' => $baoCaos
    ]);
}





public function chiTietBaoCao($maBC)
{
    $baoCao = BaoCao::with('sinhVien')->where('maBC', $maBC)->first();

    if (!$baoCao) {
        return response()->json([
            'message' => 'Không tìm thấy báo cáo.'
        ], 404);
    }

    return response()->json([
        'baoCao' => $baoCao
    ]);
}
public function xoaBaoCao($maBC)
{
    $baoCao = BaoCao::where('maBC', $maBC)->first();

    if (!$baoCao) {
        return response()->json([
            'message' => 'Không tìm thấy báo cáo.'
        ], 404);
    }

    // Xóa file đính kèm nếu tồn tại
    // if ($baoCao->tepDinhKem && Storage::disk('public')->exists($baoCao->tepDinhKem)) {
    //     Storage::disk('public')->delete($baoCao->tepDinhKem);
    // }

    $baoCao->delete();

    return response()->json([
        'message' => 'Xóa báo cáo thành công.'
    ]);
}

}
