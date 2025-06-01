<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SinhVien;

class SinhVienController extends Controller
{
    public function index()
    {
        $sinhviens = SinhVien::with(['hoSo', 'lichs', 'nhatKy', 'baoCaos'])->get();
        return response()->json($sinhviens);
    }

    public function show($maSV)
    {
        $sinhvien = SinhVien::with(['hoSo', 'lichs', 'nhatKy', 'baoCaos'])->where('maSV', $maSV)->first();

        if (!$sinhvien) {
            return response()->json(['message' => 'Không tìm thấy sinh viên'], 404);
        }

        return response()->json($sinhvien);
    }
}
