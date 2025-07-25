<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['tieuDe', 'noiDung','diemSo','doUuTien' ,'hanHoanThanh', 'trangThai','tepDinhKem'];

public function sinhViens()
{
    return $this->belongsToMany(SinhVien::class, 'sinh_vien_task', 'task_id', 'maSV', 'id', 'maSV');
}


}
