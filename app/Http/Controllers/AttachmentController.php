<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'message_id' => 'required|exists:messages,id',
        'file_url' => 'required|string',
        'file_type' => 'nullable|string',
    ]);

    $attachment = Attachment::create([
        'message_id' => $request->message_id,
        'file_url' => $request->file_url,
        'file_type' => $request->file_type,
    ]);

    return response()->json($attachment, 201);
}

}
