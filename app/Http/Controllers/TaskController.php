<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SinhVien;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
//  Tổng số task
    public function tongSoTaskTheoSinhVien($maSV)
    {
        $tong = DB::table('tasks')->where('maSV', $maSV)->count();

        return response()->json([
            'maSV' => $maSV,
            'tong_so_task' => $tong,
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'tieuDe' => 'required|string|max:255',
            'noiDung' => 'required|string',
            'maSV' => 'required|string|exists:sinh_viens,maSV',
            'doUuTien' => 'required|in:Thấp,Trung bình,Cao',
            'hanHoanThanh' => 'required|date',
        ]);

        $task = Task::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Tạo task thành công',
            'data' => $task,
        ], 201);
    }
    // GET /api/student/tasks?id=1
    // public function index(Request $request)
    // {
    //     $id = $request->query('id'); 

    //     if (!$id) {
    //         return response()->json(['message' => 'Thiếu ID sinh viên'], 400);
    //     }

    //     $sinhVien = SinhVien::where('maSV', $id)->first();


    //     if (!$sinhVien) {
    //         return response()->json(['message' => 'Sinh viên không tồn tại'], 404);
    //     }

    //     $tasks = $sinhVien->tasks()->paginate(10);

    //     return response()->json([
    //         'status' => 'success',
    //         'data' => $tasks
    //     ]);
    // }
    // GET /api/student/tasks/countTask
    public function countTasks()
    {
        $taskCount = Task::count();
        return response()->json(
            [
                'status' => 'success',
                'total_task' => $taskCount
            ]
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

    public function index(Request $request)
    {
        $search = $request->input('search');

        $tasks = Task::with('sinhVien') // Eager loading tránh N+1
            ->when($search, function ($query, $search) {
                return $query->where('tieuDe', 'like', '%' . $search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return response()->json($tasks);
    }
    public function updateDiemSo(Request $request, $id)
    {
        $request->validate([
            'diemSo' => 'required|numeric|min:0|max:10'  // hoặc tùy theo yêu cầu
        ]);

        $task = Task::findOrFail($id);
        $task->diemSo = $request->input('diemSo');
        $task->save();

        return response()->json([
            'message' => 'Cập nhật điểm số thành công!',
            'task' => $task
        ]);
    }
    public function destroy($id)
    {
        $task = Task::findOrFail($id); // nếu không tìm thấy sẽ tự trả về lỗi 404
        $task->delete();

        return response()->json([
            'message' => 'Xóa task thành công!'
        ]);
    }
}
