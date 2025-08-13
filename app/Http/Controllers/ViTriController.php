<?php

namespace App\Http\Controllers;

use App\Models\ViTri;
use Illuminate\Http\Request;

class ViTriController extends Controller
{
      public function index()
    {
        return ViTri::all();
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'tenViTri' => 'required|string|max:255|unique:vi_tris,tenViTri',
    ], [
        'tenViTri.required' => 'Tên vị trí không được để trống.',
        'tenViTri.unique'   => 'Tên vị trí đã tồn tại.',
    ]);

    $viTri = ViTri::create($validated);

    return response()->json([
        'message' => 'Thêm vị trí thành công.',
        'data' => $viTri
    ], 201);
}


     public function DsViTri(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $query = ViTri::query();
         if ($search) {
        $query->where('tenViTri', 'like', "%$search%");
    }

         $viTri = $query->orderBy('id', 'desc')->paginate($perPage);

    return response()->json([
        'data' => $viTri
    ]);
    }

  public function update(Request $request, $id)
{
    $request->validate([
        'tenViTri' => 'required|string|max:255|unique:vi_tris,tenViTri,' . $id,
    ], [
        'tenViTri.required' => 'Tên vị trí không được để trống.',
        'tenViTri.unique'   => 'Tên vị trí đã tồn tại.',
    ]);

    $viTri = ViTri::findOrFail($id);
    $viTri->update([
        'tenViTri' => $request->tenViTri,
    ]);

    return response()->json([
        'message' => 'Cập nhật vị trí thành công.',
        'data'    => $viTri
    ]);
}


    public function destroy($id)
    {
        $viTri = ViTri::findOrFail($id);
        $viTri->delete();

        return response()->json(['message' => 'Xóa thành công']);
    }


}
