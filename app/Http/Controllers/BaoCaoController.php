<?php

namespace App\Http\Controllers;

use App\Models\BaoCao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BaoCaoController extends Controller
{



public function danhSachBaoCao(Request $request)
{
    $perPage = $request->input('per_page', 10);
    $search = $request->input('search');
    $viTri = array_filter(explode(',', $request->input('vi_tri', '')));
    $truong = array_filter(explode(',', $request->input('truong', '')));
    $kyThucTap = $request->input('ky_thuc_tap');

    $query = BaoCao::with(['sinhVien.truong']) // 👈 Join cả sinh viên và trường
        ->orderBy('ngayTao', 'asc');

    // Filter theo sinh viên liên quan
    $query->whereHas('sinhVien', function ($q) use ($search, $viTri, $truong, $kyThucTap) {

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

        // Lọc theo kỳ thực tập
        if ($kyThucTap) {
            $q->where('ky_thuc_tap', $kyThucTap);
        }
    });

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
