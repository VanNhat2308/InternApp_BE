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
    ]);

    // Ưu tiên kiểm tra guard api_admin -> sau đó api_sinhvien
    $user = Auth::guard('api_admin')->user() ?? Auth::guard('api_sinhvien')->user();

    if (!$user) {
        return response()->json(['message' => 'Không xác thực'], 401);
    }

    $comment = TaskComment::create([
        'task_id'   => $request->task_id,
        'noi_dung'  => $request->noi_dung,
        'user_id'   => $user->id,
        'user_type' => get_class($user),
    ]);

    return response()->json(['message' => 'Nhận xét đã được thêm', 'data' => $comment]);
}


    public function index($task_id)
    {
        $comments = TaskComment::where('task_id', $task_id)->with('user')->latest()->get();
        return response()->json($comments);
    }
}
