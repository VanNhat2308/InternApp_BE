<?php

namespace App\Http\Controllers;

use App\Models\Truong;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        'maTruong' => [
            'required',
            'string',
            'max:255',
            Rule::unique('truongs', 'maTruong')->ignore($id)
        ],
        'tenTruong' => [
            'required',
            'string',
            'max:255',
            Rule::unique('truongs', 'tenTruong')->ignore($id)
        ],
        'moTa' => 'nullable|string',
        'logo' => 'nullable|string',
    ], [
        'maTruong.unique' => 'Mã trường đã tồn tại trong hệ thống.',
        'tenTruong.unique' => 'Tên trường đã tồn tại trong hệ thống.',
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
    $search = $request->input('search'); // lấy từ khóa tìm kiếm

    $query = Truong::query();

    if ($search) {
        $query->where('tenTruong', 'like', "%$search%")
              ->orWhere('maTruong', 'like', "%$search%");
    }

    $truongs = $query->orderBy('id', 'desc')->paginate($perPage);

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
    'tenTruong' => 'required|string|max:255|unique:truongs,tenTruong',
    'moTa' => 'nullable|string',
    'logo' => 'nullable|string',
], [
    'maTruong.unique' => 'Mã trường đã tồn tại trong hệ thống.',
    'tenTruong.unique' => 'Tên trường đã tồn tại trong hệ thống.',
]);

    $school = Truong::create($request->only('maTruong', 'tenTruong', 'moTa', 'logo'));

    return response()->json([
        'message' => 'Thêm trường thành công',
        'data' => $school,
    ]);
}

}
