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
    $tong = DB::table('sinh_vien_task')->where('maSV', $maSV)->count();

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
        'maSV' => 'required|array',
        'maSV.*' => 'exists:sinh_viens,maSV',
        'doUuTien' => 'required|in:Thấp,Trung bình,Cao',
        'hanHoanThanh' => 'required|date',
        'nguoiGiao' => 'required|string|max:255',
    ]);

    // Tạo task
    $task = Task::create([
        'tieuDe' => $validated['tieuDe'],
        'noiDung' => $validated['noiDung'],
        'doUuTien' => $validated['doUuTien'],
        'hanHoanThanh' => $validated['hanHoanThanh'],
        'trangThai' => 'Chưa nộp', // mặc định
        'nguoiGiao' => $validated['nguoiGiao'],
    ]);

    // Gán sinh viên thực hiện (nhiều-nhiều)
    $task->sinhViens()->attach($validated['maSV']);

    return response()->json([
        'message' => 'Tạo task thành công',
        'data' => $task->load('sinhViens')
    ]);
}
   
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
    $task = Task::with('sinhViens')->find($id);

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


public function listTaskSV(Request $request)
{
    $search = $request->input('search');
    $maSV = $request->input('maSV'); // lấy mã sinh viên từ query string

    $tasks = Task::with('sinhViens') // chú ý tên quan hệ là số nhiều nếu bạn định nghĩa là belongsToMany
        ->when($maSV, function ($query, $maSV) {
            return $query->whereHas('sinhViens', function ($q) use ($maSV) {
                $q->where('sinh_viens.maSV', $maSV); // thêm tên bảng để tránh ambiguity
            });
        })
        ->when($search, function ($query, $search) {
            return $query->where('tieuDe', 'like', '%' . $search . '%');
        })
        ->orderBy('created_at', 'desc')
        ->paginate(12);

    return response()->json($tasks);
}




public function index(Request $request)
{
    $search = $request->input('search');
    $status = $request->input('status');

    $tasks = Task::with('sinhViens') // tên hàm quan hệ trong model Task
        ->when($search, function ($query, $search) {
            return $query->where('tieuDe', 'like', '%' . $search . '%');
        })
        ->when($status, function ($query, $status) {
            return $query->where('trangThai', $status);
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
public function updateStatus(Request $request, $id)
{
    // Validate đầu vào
    $request->validate([
        'trangThai' => 'required|string',
        'tepDinhKem' => 'nullable|array', // Phải là mảng nếu có
    ]);

    // Tìm task theo ID
    $task = Task::findOrFail($id);

    // Cập nhật trạng thái và tệp đính kèm nếu có
    $task->trangThai = $request->trangThai;

    if ($request->has('tepDinhKem')) {
        $task->tepDinhKem = $request->tepDinhKem; // Laravel tự cast sang JSON
    }

    $task->save();

    return response()->json([
        'success' => true,
        'message' => 'Cập nhật trạng thái task thành công',
        'data' => $task, // Tự động trả tepDinhKem dạng mảng
    ]);
}


}
