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
            'tenViTri' => 'required|string|max:255',
        ]);

        $viTri = ViTri::create($validated);

        return response()->json($viTri, 201);
    }

     public function DsViTri(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        return response()->json([
            'data' => ViTri::paginate($perPage)
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tenViTri' => 'required|string|max:255',
        ]);

        $viTri = ViTri::findOrFail($id);
        $viTri->update([
            'tenViTri' => $request->tenViTri,
        ]);

        return response()->json(['message' => 'Cập nhật thành công']);
    }

    public function destroy($id)
    {
        $viTri = ViTri::findOrFail($id);
        $viTri->delete();

        return response()->json(['message' => 'Xóa thành công']);
    }


}
