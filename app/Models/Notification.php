<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'notifiable_id',
        'notifiable_type',
        'title',
        'message',
        'avatar',
        'is_read',
    ];

    /**
     * Mối quan hệ polymorphic đến Admin hoặc SinhVien
     */
    public function notifiable()
    {
        return $this->morphTo();
    }
}
