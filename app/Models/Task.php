<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['maSV', 'tieuDe', 'noiDung','diemSo','doUuTien' ,'hanHoanThanh', 'trangThai','tepDinhKem'];

    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class, 'maSV');
    }
}
