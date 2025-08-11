<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
      public function index(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'type'    => 'required|in:admin,sinhvien'
        ]);

        // Map type sang Model
        $modelType = $request->type === 'admin' 
            ? 'App\\Models\\Admin' 
            : 'App\\Models\\SinhVien';

        // Lấy danh sách thông báo, mới nhất trước
        $notifications = Notification::where('notifiable_id', $request->user_id)
            ->where('notifiable_type', $modelType)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($n) {
                return [
                    'avatar' => $n->avatar,
                    'title'  => $n->title,
                    'message'=> $n->message,
                    'time'   => $n->created_at->diffForHumans(),
                    'is_read'=> $n->is_read
                ];
            });

        return response()->json($notifications);
    }

        public function markAsRead(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'type'    => 'required|in:admin,sinhvien'
        ]);

        // Map type sang Model
        $modelType = $request->type === 'admin' 
            ? 'App\\Models\\Admin' 
            : 'App\\Models\\SinhVien';

        // Cập nhật tất cả is_read = 1 cho user này
        Notification::where('notifiable_id', $request->user_id)
            ->where('notifiable_type', $modelType)
            ->where('is_read', 0) // chỉ update cái chưa đọc
            ->update(['is_read' => 1]);

        return response()->json([
            'message' => 'Tất cả thông báo đã được đánh dấu là đã đọc.'
        ]);
    }

      public function unreadCount(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'type'    => 'required|in:admin,sinhvien'
        ]);

        // Map type sang model
        $modelType = $request->type === 'admin' 
            ? 'App\\Models\\Admin' 
            : 'App\\Models\\SinhVien';

        // Đếm số thông báo chưa đọc
        $count = Notification::where('notifiable_id', $request->user_id)
            ->where('notifiable_type', $modelType)
            ->where('is_read', 0)
            ->count();

        return response()->json([
            'unread_count' => $count
        ]);
    }

       public function deleteAll(Request $request)
    {
        $userId = $request->user_id;
        Notification::where('notifiable_id', $userId)->delete();

        return response()->json([
            'message' => 'Đã xóa toàn bộ thông báo',
        ]);
    }


    
}
