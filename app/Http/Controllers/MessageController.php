<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Conversation;
use App\Events\NewMessage;
use App\Models\Admin;
use App\Models\SinhVien;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;


class MessageController extends Controller
{

    public function show($id)
{
   
    $conversation = Conversation::find($id);

    if (!$conversation) {
        return response()->json(['message' => 'Không tìm thấy cuộc trò chuyện'], 404);
    }

    return response()->json($conversation);
}


    
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
    $adminId = $request->query('id');
    $search = $request->query('search');
    // return response()->json($search);
    $conversations = Conversation::join('messages', 'messages.id', '=', 'conversations.last_message_id')
        ->where(function ($q) use ($adminId) {
            $q->where('user1_role', 'sinhvien')->where('user2_role', 'admin')->where('user2_id', $adminId);
        })->orWhere(function ($q) use ($adminId) {
            $q->where('user2_role', 'sinhvien')->where('user1_role', 'admin')->where('user1_id', $adminId);
        })
        ->where('student_name', 'like', '%'.$search.'%')
        ->orderByDesc('messages.created_at')
        ->select('conversations.*') // tránh bị ghi đè khi join
        ->with(['lastMessage']) // eager load quan hệ
        ->take(10)
        ->get();

    // return response()->json($conversations);
    $result = $conversations->map(function ($conv) {
        $sinhvien = $conv->sinhvien; 
        $message = $conv->lastMessage;
        // Nếu lastMessage là rỗng, lấy tin nhắn gần nhất có nội dung
    if ($message && $message->content === '') {
        $prevMessage = \App\Models\Message::where('conversation_id', $conv->id)
            ->where('id', '!=', $message->id)
            ->where('content', '!=', '')
            ->orderByDesc('created_at')
            ->first();

        // Gán lại nếu tìm được
        if ($prevMessage) {
            $message = $prevMessage;
        }
   
    }

        return [
            'id' => $sinhvien->maSV ?? null,
            'name' => $sinhvien->hoTen ?? 'Không rõ',
            'preview' => Str::limit($message->content ?? '', 50),
            'time' => $message->created_at?->diffForHumans() ?? '',
            'unread' => $message->is_read,
            'conversation_id' => $conv->id,
        ];
    });


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

 $admin = Admin::where('maAdmin', $from_role === 'admin' ? $from_id : $to_id)->first();
$sinhvien = SinhVien::where('maSV', $from_role === 'sinhvien' ? $from_id : $to_id)->first();
if (!$admin || !$sinhvien) {
    return response()->json(['message' => 'Không tìm thấy admin hoặc sinh viên'], 404);
}

    // Nếu chưa có thì tạo
    if (!$conversation) {
        $conversation = Conversation::create([
            'user1_id' => $from_id,
            'user1_role' => $from_role,
            'user2_id' => $to_id,
            'user2_role' => $to_role,
            'admin_name'=>$admin->hoTen,
            'student_name'=>$sinhvien->hoTen
        ]);




    }

            $message = Message::create([
    'from_id'         => $from_id,
    'from_role'       =>  $from_role,
    'to_id'           => $to_id,
    'to_role'         => $to_role,
    'conversation_id' => $conversation->id,
    'content'         => "",
    'type'            => 'text', 
    'is_read'         => false,
]);

 Conversation::where('id', $conversation->id)->update([
            'last_message_id' => $message->id,
            'updated_at' => now(),
        ]);
    return response()->json([
        'conversation_id' => $conversation->id,
    ]);
}

public function destroy($id)
{
    DB::beginTransaction();

    try {
        $conversation = Conversation::findOrFail($id);

        // Xoá tất cả message liên quan đến conversation này
        $conversation->messages()->delete();

        // Xoá conversation
        $conversation->delete();

        DB::commit();

        return response()->json([
            'message' => 'Conversation và tất cả tin nhắn đã được xoá.'
        ]);
    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'error' => 'Đã xảy ra lỗi khi xoá conversation.',
            'details' => $e->getMessage(),
        ], 500);
    }
}

}