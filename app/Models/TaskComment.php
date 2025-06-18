<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskComment extends Model
{
    protected $fillable = [
        'task_id',
        'user_id',
        'user_type',
        'noi_dung',
    ];

    // Quan hệ đa hình ngược
    public function user()
    {
        return $this->morphTo();
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
