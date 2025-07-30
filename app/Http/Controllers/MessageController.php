<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Conversation;
use App\Events\NewMessage;
use Illuminate\Support\Str;


class MessageController extends Controller
{


    
public function markAsRead(Request $request)
{
    $conversationId = $request->input('conversation_id');
    $userId = $request->input('user_id');
    $userRole = $request->input('user_role');

    // Cập nhật tất cả các tin nhắn chưa đọc gửi đến user hiện tại
    Message::where('conversation_id', $conversationId)
        ->where('to_id', $userId)
        ->where('to_role', $userRole)
        ->where('is_read', false)
        ->update(['is_read' => true]);

    return response()->json(['message' => 'Đã cập nhật trạng thái is_read'], 200);
}


    public function feedbackListForStudent(Request $request)
{
    $sinhvienId = $request->query('id'); // maSV của sinh viên hiện tại
    $search = $request->query('search');

    // Tin nhắn mới nhất từ mỗi admin gửi đến sinh viên
    $latestMessages = Message::where([
            ['to_role', '=', 'sinhvien'],
            ['to_id', '=', $sinhvienId],
            ['from_role', '=', 'admin'],
        ])
        ->latest('created_at')
        ->get()
        ->unique('from_id') // mỗi admin chỉ xuất hiện 1 lần
        ->take(10);

    // Nạp thông tin admin
    $latestMessages->load('adminSender');

    // Format lại phản hồi
    $result = $latestMessages->map(function ($msg) {
        $admin = $msg->adminSender;

        return [
            'id' => $admin->maAdmin ?? null,
            'name' => $admin->hoTen ?? 'Không rõ',
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
       $adminId = $request->query('id'); // maAdmin của admin hiện tại
    $search = $request->query('search');

    // Tin nhắn mới nhất từ mỗi sinh viên gửi đến admin
    $latestMessages = Message::where([
            ['to_role', '=', 'admin'],
            ['to_id', '=', $adminId],
            ['from_role', '=', 'sinhvien'],
        ])
        ->latest('created_at')
        ->get()
        ->unique('from_id') // mỗi sinh viên chỉ xuất hiện 1 lần
        ->take(10);

    // Nạp thông tin sinh viên
    $latestMessages->load('sinhvienSender');

    // Format lại phản hồi
    $result = $latestMessages->map(function ($msg) {
        $sinhvien = $msg->sinhvienSender;

        return [
            'id' => $sinhvien->maSV ?? null,
            'name' => $sinhvien->hoTen ?? 'Không rõ',
            'preview' => \Illuminate\Support\Str::limit($msg->content, 50),
            'time' => $msg->created_at->diffForHumans(),
            'unread' => !$msg->is_read,
            'conversation_id' => $msg->conversation_id ?? null,
        ];
    });

    // Lọc theo tên nếu có từ khóa
    if ($search) {
        $result = $result->filter(function ($item) use ($search) {
            return \Illuminate\Support\Str::contains(\Illuminate\Support\Str::lower($item['name']), \Illuminate\Support\Str::lower($search));
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

    public function findOrCreateConversation(Request $request)
{
    $request->validate([
        'from_id'   => 'required|integer',
        'from_role' => 'required|in:sinhvien,admin',
        'to_id'     => 'required|integer',
        'to_role'   => 'required|in:sinhvien,admin',
    ]);

    $from_id = $request->input('from_id');
    $from_role = $request->input('from_role');
    $to_id = $request->input('to_id');
    $to_role = $request->input('to_role');

    // Tìm hội thoại 2 chiều giữa from/to và to/from
$conversation = Conversation::where(function ($q) use ($from_id, $from_role, $to_id, $to_role) {
    $q->where(function ($query) use ($from_id, $from_role, $to_id, $to_role) {
        $query->where('user1_id', $from_id)
              ->where('user1_role', $from_role)
              ->where('user2_id', $to_id)
              ->where('user2_role', $to_role);
    })->orWhere(function ($query) use ($from_id, $from_role, $to_id, $to_role) {
        $query->where('user1_id', $to_id)
              ->where('user1_role', $to_role)
              ->where('user2_id', $from_id)
              ->where('user2_role', $from_role);
    });
})->first();


    // Nếu chưa có thì tạo
    if (!$conversation) {
        $conversation = Conversation::create([
            'user1_id' => $from_id,
            'user1_role' => $from_role,
            'user2_id' => $to_id,
            'user2_role' => $to_role,
        ]);
    }

    return response()->json([
        'conversation_id' => $conversation->id,
    ]);
}

}