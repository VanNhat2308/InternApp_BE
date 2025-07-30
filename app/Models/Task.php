<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $casts = [
    'tepDinhKem' => 'array',
];

    protected $fillable = ['tieuDe', 'noiDung','diemSo','doUuTien' ,'hanHoanThanh', 'trangThai','tepDinhKem'];

public function sinhViens()
{
    return $this->belongsToMany(SinhVien::class, 'sinh_vien_task', 'task_id', 'maSV', 'id', 'maSV');
}
public function taskComments()
{
    return $this->hasMany(TaskComment::class);
}


}
