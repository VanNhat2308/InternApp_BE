<?php

namespace App\Http\Controllers;

use App\Models\Truong;
use Illuminate\Http\Request;

class TruongController extends Controller
{

    public function destroy($id)
{
    $truong = Truong::find($id);

    if (!$truong) {
        return response()->json([
            'message' => 'Không tìm thấy trường cần xóa.'
        ], 404);
    }

    $truong->delete();

    return response()->json([
        'message' => 'Xóa trường thành công.'
    ]);
}

    public function update(Request $request, $id)
{
    $request->validate([
        'maTruong' => 'required|string|max:255',
        'tenTruong' => 'required|string|max:255',
        'moTa' => 'nullable|string',
         'logo' => 'nullable|string', 
    ]);

    $truong = Truong::findOrFail($id);

    $truong->update([
        'maTruong' => $request->maTruong,
        'tenTruong' => $request->tenTruong,
        'moTa' => $request->moTa,
        'logo' => $request->logo,
    ]);

    return response()->json([
        'message' => 'Cập nhật trường thành công.',
        'data' => $truong
    ]);
}

    public function DsTruong(Request $request)
{
    $perPage = $request->input('per_page', 10);
    
    $truongs = Truong::orderBy('id', 'desc')->paginate($perPage);

    return response()->json([
        'data' => $truongs
    ]);
}
     public function index()
    {
        return Truong::all();
    }
    

  
public function store(Request $request)
{
    $request->validate([
        'maTruong' => 'required|string|max:255|unique:truongs,maTruong',
        'tenTruong' => 'required|string|max:255',
        'moTa' => 'nullable|string',
        'logo' => 'nullable|string', // 👈 Đường dẫn logo từ client
    ]);

    $school = Truong::create($request->only('maTruong', 'tenTruong', 'moTa', 'logo'));

    return response()->json([
        'message' => 'Thêm trường thành công',
        'data' => $school,
    ]);
}

}
