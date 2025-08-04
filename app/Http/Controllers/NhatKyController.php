<?php

namespace App\Http\Controllers;

use App\Models\ChiTietNhatKy;
use App\Models\NhatKy;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class NhatKyController extends Controller
{

public function capNhatTrangThai($id)
{
    $nhatKy = NhatKy::findOrFail($id);

    // Đảo trạng thái hiện tại
    $nhatKy->trangThai = $nhatKy->trangThai === 'Hoàn thành' ? 'Chưa xong' : 'Hoàn thành';
    $nhatKy->save();

    return response()->json([
        'status' => 'success',
        'message' => 'Cập nhật trạng thái thành công',
        'data' => $nhatKy,
    ]);
}


public function themChiTiet(Request $request, $id)
{
    // Validate dữ liệu
    $validated = $request->validate([
        'tenCongViec' => 'required|string|max:255',
        'ketQua' => 'required|string|max:1000',
        'tienDo' => 'required|in:Hoàn thành,Chưa xong',
        'ngayThucHien' => 'required|date_format:d/m/Y', // validate đúng định dạng nhập vào
    ]);

    // Chuyển định dạng ngày thành yyyy-mm-dd
    $ngayThucHien = Carbon::createFromFormat('d/m/Y', $validated['ngayThucHien'])->format('Y-m-d');

    // Kiểm tra nhật ký có tồn tại
    $nhatKy = NhatKy::findOrFail($id);

    // Tạo mới chi tiết nhật ký
    $chiTiet = new ChiTietNhatKy([
        'tenCongViec' => $validated['tenCongViec'],
        'ketQua' => $validated['ketQua'],
        'tienDo' => $validated['tienDo'],
        'ngayThucHien' => $ngayThucHien, // đã chuyển định dạng
    ]);

    $nhatKy->chiTietNhatKies()->save($chiTiet);

    return response()->json([
        'message' => 'Thêm chi tiết thành công',
        'data' => $chiTiet,
    ], 201);
}


  public function destroy($nhatKyId, $chiTietId)
{
 


    // Tìm chi tiết thuộc đúng nhật ký
    $chiTiet = ChiTietNhatKy::where('id', $chiTietId)
        ->where('maNK', $nhatKyId)
        ->first();

    if (!$chiTiet) {
        return response()->json([
            'message' => 'Chi tiết nhật ký không tồn tại hoặc không thuộc nhật ký này',
        ], 404);
    }

    $chiTiet->delete();

    return response()->json([
        'message' => 'Xoá chi tiết thành công',
    ]);
}  
public function updateChiTietNK(Request $request, $nhatKyId, $chiTietId)
{
    // Validate input
    $request->validate([
        'tenCongViec' => 'required|string|max:255',
        'ketQua' => 'required|string',
        'tienDo' => 'required|in:Hoàn thành,Chưa xong',
    ]);

    // Tìm chi tiết nhật ký theo ID và kiểm tra nó có thuộc nhật ký có idSlug không (bảo vệ)
    $chiTiet = ChiTietNhatKy::where('id', $chiTietId)
        ->where('maNK', $nhatKyId)
        ->first();

    if (!$chiTiet) {
        return response()->json(['message' => 'Chi tiết nhật ký không tồn tại'], 404);
    }

    // Cập nhật dữ liệu
    $chiTiet->update([
        'tenCongViec' => $request->tenCongViec,
        'ketQua' => $request->ketQua,
        'tienDo' => $request->tienDo,
    ]);

    return response()->json([
        'message' => 'Cập nhật thành công',
        'data' => $chiTiet,
    ]);
}

public function storeOrUpdateChiTiet(Request $request, $maNK)
{
    $data = $request->validate([
        'id' => 'nullable|exists:chi_tiet_nhat_kies,id',
        'tenCongViec' => 'required|string',
        'ketQua' => 'nullable|string',
        'tienDo' => 'required|in:Hoàn thành,Chưa xong',
        'ngayThucHien' => 'required|date_format:d/m/Y', // thêm validate ngày
    ]);

    // Chuyển đổi định dạng từ d/m/Y -> Y-m-d để lưu vào DB
    $data['ngayThucHien'] = \Carbon\Carbon::createFromFormat('d/m/Y', $data['ngayThucHien'])->format('Y-m-d');
    $data['maNK'] = $maNK;

    $chiTiet = ChiTietNhatKy::updateOrCreate(
        ['id' => $data['id'] ?? null],
        $data
    );

    return response()->json([
        'message' => $request->id ? 'Cập nhật chi tiết thành công' : 'Thêm chi tiết thành công',
        'data' => $chiTiet
    ]);
}

    public function NhatKyTheoMaNK($maNK)
{
    $nhatKy = NhatKy::with('chiTietNhatKies')->find($maNK);

    if (!$nhatKy) {
        return response()->json([
            'message' => 'Không tìm thấy nhật ký'
        ], 404);
    }

    return response()->json([
        'message' => 'Lấy nhật ký thành công',
        'data' => $nhatKy
    ]);
}


 public function listDiary(Request $request, $maSV)
{
    $keyword = $request->input('keyword');

    $query = NhatKy::where('maSV', $maSV);

    // Nếu có từ khóa tìm kiếm thì thêm điều kiện lọc
    if (!empty($keyword)) {
        $query->where('noiDung', 'like', '%' . $keyword . '%');
    }

    $nhatKys = $query->orderBy('ngayTao', 'desc')
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
