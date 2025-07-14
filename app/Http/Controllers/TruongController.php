<?php

namespace App\Http\Controllers;

use App\Models\Truong;
use Illuminate\Http\Request;

class TruongController extends Controller
{
     public function index()
    {
        return Truong::all();
    }
    

  
public function store(Request $request)
{
    $validated = $request->validate([
        'maTruong'   => 'required|string|unique:truongs,maTruong',
        'tenTruong'  => 'required|string|max:255',
        'moTa'       => 'nullable|string',
    ]);

    $truong = Truong::create($validated);

    return response()->json([
        'message' => 'Thêm trường thành công',
        'data'    => $truong,
    ], 201);
}
}
