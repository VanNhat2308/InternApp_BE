<?php

namespace App\Http\Controllers;

use App\Models\ChiTietNhatKy;
use App\Models\NhatKy;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NhatKyController extends Controller
{

        public function listDiary($maSV)
    {
        $nhatKys = NhatKy::where('maSV', $maSV)
            ->orderBy('ngayTao', 'desc')
            ->get(['maNK', 'ngayTao', 'noiDung', 'trangThai', 'maSV']);

        return response()->json([
            'success' => true,
            'data' => $nhatKys
        ]);
    }
      public function index($maSV)
    {
        $nhatKys = NhatKy::with('chiTietNhatKies')
            ->where('maSV', $maSV)
            ->orderBy('ngayTao', 'desc')
            ->get();

        return response()->json($nhatKys);
    }

    public function store(Request $request)
{
    $data = $request->validate([
        'ngayTao' => 'nullable|date',
        'noiDung' => 'nullable|string',
        'trangThai' => 'nullable|string',
        'maSV' => 'required|exists:sinh_viens,maSV',
    ]);
    $data['maNK'] = (string) Str::uuid();
    $nhatKy = NhatKy::create($data);

    return response()->json([
        'message' => 'Tạo nhật ký thành công',
        'data' => $nhatKy
    ]);
}
public function storeDetail(Request $request)
{
    $data = $request->validate([
        'maNK' => 'required|exists:nhat_kies,maNK',
        'tenCongViec' => 'required|string',
        'ketQua' => 'nullable|string',
        'tienDo' => 'required|in:Hoàn thành,Chưa xong',
    ]);

    $chiTiet = ChiTietNhatKy::create($data);

    return response()->json([
        'message' => 'Tạo chi tiết nhật ký thành công',
        'data' => $chiTiet
    ]);
}

}
