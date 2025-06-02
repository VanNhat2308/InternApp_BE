<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SinhVien;
use App\Models\Task;


class TaskController extends Controller
{
    // GET /api/student/tasks?id=1
    public function index(Request $request)
    {
        $id = $request->query('id'); // Lấy ID sinh viên từ query string

        if (!$id) {
            return response()->json(['message' => 'Thiếu ID sinh viên'], 400);
        }

        $sinhVien = SinhVien::where('maSV', $id)->first();


        if (!$sinhVien) {
            return response()->json(['message' => 'Sinh viên không tồn tại'], 404);
        }

        $tasks = $sinhVien->tasks()->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $tasks
        ]);
    }
        // GET /api/student/tasks/countTask
        public function countTasks()
        {
            $taskCount = Task::count();
            return response()->json([
                'status' => 'success',
                'total_task' => $taskCount]
            );

        }

        public function show($id)
{
    $task = Task::with('sinhVien')->find($id);

    if (!$task) {
        return response()->json([
            'status' => 'error',
            'message' => 'Task không tồn tại'
        ], 404);
    }

    return response()->json([
        'status' => 'success',
        'data' => $task
    ]);
}


}
