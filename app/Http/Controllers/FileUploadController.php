<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; 

class FileUploadController extends Controller
{
    
public function upload(Request $request)
{
    $request->validate([
        'avatar'    => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        'cv'        => 'nullable|file|mimes:pdf,jpeg,jpg,png|max:2048',
        'task'      => 'nullable|array',
        'task.*'    => 'file|mimes:pdf,jpeg,jpg,png|max:2048',
        'logo'      => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
    ]);

    $paths = [];

    if ($request->hasFile('avatar')) {
        $file = $request->file('avatar');
        $stored = $file->store('avatars', 'public');
        $paths['avatar'] = [
            'name' => $file->getClientOriginalName(),
            'path' => $stored
        ];
    }

    if ($request->hasFile('cv')) {
        $file = $request->file('cv');
        $stored = $file->store('cvs', 'public');
        $paths['cv'] = [
            'name' => $file->getClientOriginalName(),
            'path' => $stored
        ];
    }

    if ($request->hasFile('task')) {
        $paths['task'] = [];
        foreach ($request->file('task') as $file) {
            $stored = $file->store('tasks', 'public');
            $paths['task'][] = [
                'name' => $file->getClientOriginalName(),
                'path' => $stored
            ];
        }
    }

    if ($request->hasFile('logo')) {
        $file = $request->file('logo');
        $stored = $file->store('logos', 'public');
        $paths['logo'] = [
            'name' => $file->getClientOriginalName(),
            'path' => $stored
        ];
    }

    return response()->json([
        'message' => 'Files uploaded successfully',
        'paths' => $paths,
    ]);
}





}
