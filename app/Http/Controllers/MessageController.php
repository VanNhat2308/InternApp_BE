<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Conversation;
use App\Events\NewMessage;
use Illuminate\Support\Str;


class MessageController extends Controller
{

public function feedbackList(Request $request)
{
    $adminId = 1;
    $search = $request->query('search'); // Lấy từ khóa tìm kiếm

    // Lấy 50 tin nhắn gần nhất gửi đến admin từ sinh viên
    $latestMessages = Message::where('to_role', 'admin')
        ->where('to_id', $adminId)
        ->where('from_role', 'sinhvien')
        ->latest('created_at')
        ->limit(50)
        ->get()
        ->unique('from_id')
        ->take(10);

    // Load quan hệ sinhvienSender
    $latestMessages->load('sinhvienSender');

    // Format dữ liệu phản hồi
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

    // Nếu có từ khóa tìm kiếm, lọc lại theo tên sinh viên
    if ($search) {
        $result = $result->filter(function ($item) use ($search) {
            return Str::contains(Str::lower($item['name']), Str::lower($search));
        });
    }

    return response()->json($result->values()); // values() để reset key
}
    // 🔹 Lấy danh sách tin nhắn của 1 cuộc hội thoại
    public function getMessages($conversationId)
    {
        $messages = Message::where('conversation_id', $conversationId)
            ->orderBy('created_at')
            ->get();

        return response()->json($messages);
    }

    // 🔹 Gửi tin nhắn mới
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

        // Cập nhật last_message_id của conversation
        Conversation::where('id', $data['conversation_id'])->update([
            'last_message_id' => $message->id,
            'updated_at' => now()
        ]);

        // Gửi realtime đến Pusher
        broadcast(new NewMessage($message))->toOthers();

        return response()->json($message);
    }
}
