<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['maSV', 'tieuDe', 'noiDung', 'hanHoanThanh', 'trangThai'];

    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class, 'maSV');
    }
}
