<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; 

class FileUploadController extends Controller
{
    
public function upload(Request $request)
{
    $request->validate([
        'avatar' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        'cv' => 'nullable|file|mimes:pdf,jpeg,jpg,png|max:2048',
        'task' => 'nullable|file|mimes:pdf,jpeg,jpg,png|max:2048',
    
    ]);

    $paths = [];

    if ($request->hasFile('avatar')) {
        $paths['avatar'] = $request->file('avatar')->store('avatars', 'public');
    }

    if ($request->hasFile('cv')) {
        $paths['cv'] = $request->file('cv')->store('cvs', 'public');
    }

    if ($request->hasFile('task')) {
    $path = $request->file('task')->store('tasks', 'public'); // Lưu vào storage/app/public/tasks
    $paths['task'] = $path;
}


    return response()->json([
        'message' => 'Files uploaded successfully',
        'paths' => $paths,
    ]);
}


}
