<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Conversation;
use App\Events\NewMessage;
use Illuminate\Support\Str;


class MessageController extends Controller
{
    /**
     * 🔹 Lấy danh sách tin nhắn trong 1 cuộc hội thoại
     */
    public function getMessages($conversationId)
    {
        $messages = Message::where('conversation_id', $conversationId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    /**
     * 🔹 Danh sách sinh viên đã nhắn tin với admin (panel bên trái)
     */
    public function feedbackList(Request $request)
    {
        $adminId = 1;
        $search = $request->query('search');

        // Tin nhắn mới nhất từ mỗi sinh viên gửi đến admin
        $latestMessages = Message::where([
                ['to_role', '=', 'admin'],
                ['to_id', '=', $adminId],
                ['from_role', '=', 'sinhvien'],
            ])
            ->latest('created_at')
            ->get()
            ->unique('from_id')  // chỉ lấy 1 sinh viên 1 lần
            ->take(10);

        // Nạp thông tin sinh viên
        $latestMessages->load('sinhvienSender');

        // Format lại dữ liệu phản hồi
        $result = $latestMessages->map(function ($msg) {
            $sinhvien = $msg->sinhvienSender;

            return [
                'id' => $sinhvien->maSV ?? null,
                'name' => $sinhvien->hoTen ?? 'Không rõ',
                'preview' => Str::limit($msg->content, 50),
                'time' => $msg->created_at->diffForHumans(),
                'unread' => !$msg->is_read,
                'conversation_id' => $msg->conversation_id ?? null,
            ];
        });

        // Lọc theo tên nếu có từ khóa
        if ($search) {
            $result = $result->filter(function ($item) use ($search) {
                return Str::contains(Str::lower($item['name']), Str::lower($search));
            });
        }

        return response()->json($result->values());
    }

    /**
     * 🔹 Gửi tin nhắn mới
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'from_id'         => 'required|integer',
            'from_role'       => 'required|in:sinhvien,admin',
            'to_id'           => 'required|integer',
            'to_role'         => 'required|in:sinhvien,admin',
            'conversation_id' => 'required|exists:conversations,id',
            'content'         => 'required|string',
            'type'            => 'in:text,image,file,audio',
        ]);

        $message = Message::create([
            ...$data,
            'is_read' => false,
        ]);

        // Cập nhật thông tin hội thoại
        Conversation::where('id', $data['conversation_id'])->update([
            'last_message_id' => $message->id,
            'updated_at' => now(),
        ]);

        // Gửi realtime đến Pusher (bỏ dòng này nếu chưa dùng Pusher)
        broadcast(new NewMessage($message))->toOthers();

        return response()->json($message);
    }
}