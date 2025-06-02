<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SinhVien;
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



    // Lấy danh sách tất cả sinh viên
    public function index()
    {
        $sinhViens = SinhVien::all();

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
