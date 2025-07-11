<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleSwap extends Model
{
     use HasFactory;

    protected $table = 'schedule_swaps';

    protected $fillable = [
        'maSV',
        'maLich',
        'old_date',
        'old_shift',
        'new_date',
        'new_shift',
        'change_type',
        'reason',
        'status',
        'admin_note',
    ];

    // Quan hệ với bảng sinh viên (nếu có model SinhVien)
    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class, 'maSV', 'maSV');
    }
    public function lich()
    {
        return $this->belongsTo(Lich::class, 'maLich', 'maLich');
    }
}
