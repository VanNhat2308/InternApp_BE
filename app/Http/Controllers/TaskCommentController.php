<?php

namespace App\Http\Controllers;

use App\Models\TaskComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class TaskCommentController extends Controller
{
 public function store(Request $request)
{
    $request->validate([
        'task_id' => 'required|exists:tasks,id',
        'noi_dung' => 'required|string',
        'user_id' => 'required|integer',
        'user_type' => 'required|string', // "App\Models\Admin" hoặc "App\Models\SinhVien"
    ]);

    $comment = TaskComment::create([
        'task_id'   => $request->task_id,
        'noi_dung'  => $request->noi_dung,
        'user_id'   => $request->user_id,
        'user_type' => $request->user_type,
    ]);

    return response()->json(['message' => 'Nhận xét đã được thêm', 'data' => $comment]);
}


    public function index($task_id)
    {
        $comments = TaskComment::where('task_id', $task_id)->with('user') -> orderBy('created_at', 'asc') ->get();
        return response()->json($comments);
    }
}
