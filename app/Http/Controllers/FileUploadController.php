<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; 

class FileUploadController extends Controller
{
    
    public function upload(Request $request)
    {
        // Validate file
        $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,png|max:2048', // 2MB max
        ]);

        // Store file
        $path = $request->file('file')->store('uploads', 'public'); // lưu vào storage/app/public/uploads

        return response()->json([
            'message' => 'File uploaded successfully',
            'path' => $path,
        ]);
    }
}
