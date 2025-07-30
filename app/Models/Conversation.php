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

    public function lastMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'last_message_id');
    }

    // ✅ Quan hệ chính xác
    public function messages()
    {
        return $this->hasMany(Message::class, 'conversation_id');
    }

    // ✅ Tìm hoặc tạo hội thoại 2 chiều
    public static function findOrCreate($role1, $id1, $role2, $id2)
    {
        return self::firstOrCreate(
            [
                ['user1_role', '=', $role1],
                ['user1_id', '=', $id1],
                ['user2_role', '=', $role2],
                ['user2_id', '=', $id2],
            ],
            [
                'user1_role' => $role1,
                'user1_id'   => $id1,
                'user2_role' => $role2,
                'user2_id'   => $id2,
            ]
        );
    }
public function getSinhvienAttribute()
{
    if ($this->user1_role === 'sinhvien') {
        return \App\Models\SinhVien::find($this->user1_id);
    } elseif ($this->user2_role === 'sinhvien') {
        return \App\Models\SinhVien::find($this->user2_id);
    }

    return null;
}


}
