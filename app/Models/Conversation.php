<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Conversation extends Model
{
    protected $table = 'conversations';

    protected $fillable = [
        'user1_role',
        'user1_id',
        'user2_role',
        'user2_id',
        'last_message_id',
    ];

    public $timestamps = false;

    protected $casts = [
        'updated_at' => 'datetime',
    ];

    // Quan hệ đến tin nhắn cuối cùng
    public function lastMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'last_message_id');
    }

    // Lấy tất cả các tin nhắn trong hội thoại
    public function messages()
    {
        return Message::where(function ($query) {
            $query->where('from_role', $this->user1_role)
                ->where('from_id', $this->user1_id)
                ->where('to_role', $this->user2_role)
                ->where('to_id', $this->user2_id);
        })->orWhere(function ($query) {
            $query->where('from_role', $this->user2_role)
                ->where('from_id', $this->user2_id)
                ->where('to_role', $this->user1_role)
                ->where('to_id', $this->user1_id);
        });
    }
    public static function findOrCreate($role1, $id1, $role2, $id2)
{
    $conversation = Conversation::where(function ($q) use ($role1, $id1, $role2, $id2) {
        $q->where('user1_role', $role1)->where('user1_id', $id1)
          ->where('user2_role', $role2)->where('user2_id', $id2);
    })->orWhere(function ($q) use ($role1, $id1, $role2, $id2) {
        $q->where('user1_role', $role2)->where('user1_id', $id2)
          ->where('user2_role', $role1)->where('user2_id', $id1);
    })->first();

    if (!$conversation) {
        $conversation = self::create([
            'user1_role' => $role1,
            'user1_id' => $id1,
            'user2_role' => $role2,
            'user2_id' => $id2,
        ]);
    }

    return $conversation;
}

}
