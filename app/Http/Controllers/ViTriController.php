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


}
