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
}
