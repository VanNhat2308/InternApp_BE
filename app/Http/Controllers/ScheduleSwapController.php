<?php

namespace App\Http\Controllers;

use App\Events\NewNotification;
use App\Models\Admin;
use App\Models\Lich;
use App\Models\Notification;
use App\Models\ScheduleSwap;
use App\Models\SinhVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ScheduleSwapController extends Controller
{
    // GET: Lấy danh sách đổi ca của sinh viên
public function index(Request $request)
{
    $perPage = $request->query('per_page', 10); // Số dòng mỗi trang, mặc định là 10

    $query = ScheduleSwap::with(['sinhVien:maSV,hoTen'])
                ->orderByDesc('created_at');

    // Lọc theo tên nếu có
    if ($request->filled('search')) {
        $hoTen = $request->query('search');

        $query->whereHas('sinhVien', function ($q) use ($hoTen) {
            $q->where('hoTen', 'like', '%' . $hoTen . '%');
        });
    }

    $paginated = $query->paginate($perPage);

    // Thêm hoTen vào từng phần tử trong kết quả
    $data = $paginated->getCollection()->map(function ($swap) {
        return [
            ...$swap->toArray(),
            'hoTen' => $swap->sinhVien->hoTen ?? null,
        ];
    });

    // Trả về dữ liệu kèm thông tin phân trang
    return response()->json([
        'data' => $data,
        'current_page' => $paginated->currentPage(),
        'last_page' => $paginated->lastPage(),
        'per_page' => $paginated->perPage(),
        'total' => $paginated->total(),
    ]);
}



    // POST: Tạo yêu cầu đổi ca mới
    public function store(Request $request)
    {
        $validated = $request->validate([
            'maSV'        => 'required|exists:sinh_viens,maSV',
            'maLich'      => 'required|exists:lichs,maLich',
            'old_date'    => 'required|date',
            'old_shift'   => 'required|string',
            'change_type' => 'required|in:doi,nghi',
            'reason'      => 'required|string',

            // Nếu là đổi, yêu cầu thêm new_date và new_shift
            'new_date'    => 'nullable|date',
            'new_shift'   => 'nullable|string',
        ]);

        // Nếu là "doi" thì bắt buộc có new_date + new_shift
        if ($validated['change_type'] === 'doi') {
            if (empty($validated['new_date']) || empty($validated['new_shift'])) {
                return response()->json([
                    'message' => 'Vui lòng cung cấp ca mới để đổi.',
                ], 422);
            }
        }

        $swap = ScheduleSwap::create($validated);

            // 3. Lấy thông tin admin từ maAdmin
    $sinhvien = SinhVien::where('maSV', $validated['maSV'])->first();
    $admin = Admin::where('maAdmin', 1)->first();

    if ($admin) {
        // 4. Tạo thông báo
        $notification = $admin->notifications()->create([
            'title'   => 'Yêu cầu đổi lịch',
            'message' => 'Bạn nhận được yêu cầu đổi lịch từ '. $sinhvien->hoTen,
            'avatar'  => '/images/task.png', // icon minh hoạ
        ]);
    $unreadCount = Notification::where('notifiable_id', $admin->maAdmin)
    ->where('is_read', 0)
    ->count();

        // 5. Bắn event qua Pusher (đúng channel admin.{id})
        broadcast(new NewNotification($notification, "admin.{$admin->maAdmin}",$unreadCount));
    }



        return response()->json([
            'message' => 'Gửi yêu cầu đổi ca thành công.',
            'data'    => $swap
        ], 201);
    }
    public function xuLyDoiLich($id)
{
    $swap = ScheduleSwap::find($id);

    if (!$swap || $swap->status !== 'pending') {
        return response()->json(['message' => 'Yêu cầu không hợp lệ'], 400);
    }

    DB::beginTransaction();
    try {
        // Xóa lịch cũ
        $caTime = ['08:00-12:00' => '08:00', '13:00-17:00' => '13:00'];
        $lich = Lich::where('maSV', $swap->maSV)
            ->where('ngay', $swap->old_date)
            ->where('time', $caTime[$swap->old_shift])
            ->first();

        if ($lich) {
            $lich->delete();
        }

        // Tạo lịch mới nếu là 'doi'
        if ($swap->change_type === 'doi') {
            $mapCa = [
                '08:00-12:00' => ['time' => '08:00', 'duration' => 4],
                '13:00-17:00' => ['time' => '13:00', 'duration' => 4],
            ];
            Lich::create([
                'maLich' => 'LICH' . strtoupper(uniqid()),
                'maSV' => $swap->maSV,
                'ngay' => $swap->new_date,
                'time' => $mapCa[$swap->new_shift]['time'],
                'duration' => $mapCa[$swap->new_shift]['duration'],
                'noiDung' => 'Đổi lịch đã được duyệt',
                'trangThai' => 'Chưa học',
            ]);
        }

        $swap->status = 'approved';
        $swap->save();

        DB::commit();

               // 3. Lấy thông tin admin từ maAdmin
    $sinhvien = SinhVien::where('maSV', $swap->maSV)->first();


    if ($sinhvien) {
        // 4. Tạo thông báo
        $notification = $sinhvien->notifications()->create([
            'title'   => 'Yêu cầu đổi lịch',
            'message' => 'Yêu cầu đổi lịch của bạn đã được chấp nhận ',
            'avatar'  => '/images/task.png', // icon minh hoạ
        ]);
    $unreadCount = Notification::where('notifiable_id',$sinhvien->maSV)
    ->where('is_read', 0)
    ->count();

        // 5. Bắn event qua Pusher (đúng channel admin.{id})
        broadcast(new NewNotification($notification, "sinhvien.{$sinhvien->maSV}",$unreadCount));
    }



        return response()->json(['message' => 'Duyệt và đổi lịch thành công']);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'Lỗi xử lý: ' . $e->getMessage()], 500);
    }
}

    public function updateStatus(Request $request, $id)
    {
    $validated = $request->validate([
        'status' => ['required', Rule::in(['approved', 'rejected'])],
        'admin_note' => 'nullable|string',
    ]);

    $swap = ScheduleSwap::find($id);

    if (!$swap) {
        return response()->json(['message' => 'Yêu cầu đổi ca không tồn tại.'], 404);
    }

    if ($swap->status !== 'pending') {
        return response()->json(['message' => 'Yêu cầu này đã được xử lý.'], 400);
    }

    if ($validated['status'] === 'approved') {
        // Gọi hàm xử lý đổi lịch
        $xuLyResult = $this->xuLyDoiLich($id);

        // Nếu xử lý đổi lịch thất bại, dừng lại
        if ($xuLyResult->getStatusCode() !== 200) {
            return $xuLyResult;
        }

        // Ghi chú bổ sung (nếu có)
        $swap->admin_note = $validated['admin_note'] ?? null;
        $swap->save();

        return response()->json([
            'message' => 'Duyệt và đổi lịch thành công.',
            'data' => $swap
        ]);
    }

    // Trường hợp từ chối
    $swap->status = 'rejected';
    $swap->admin_note = $validated['admin_note'] ?? null;
    $swap->save();
                   // 3. Lấy thông tin admin từ maAdmin
    $sinhvien = SinhVien::where('maSV', $swap->maSV)->first();


    if ($sinhvien) {
        // 4. Tạo thông báo
        $notification = $sinhvien->notifications()->create([
            'title'   => 'Yêu cầu đổi lịch',
            'message' => 'Yêu cầu đổi lịch của bạn đã bị từ chối',
            'avatar'  => '/images/task.png', // icon minh hoạ
        ]);
    $unreadCount = Notification::where('notifiable_id',$sinhvien->maSV)
    ->where('is_read', 0)
    ->count();

        // 5. Bắn event qua Pusher (đúng channel admin.{id})
        broadcast(new NewNotification($notification, "sinhvien.{$sinhvien->maSV}",$unreadCount));
    }

    return response()->json([
        'message' => 'Từ chối yêu cầu thành công.',
        'data' => $swap
    ]);
}
}
