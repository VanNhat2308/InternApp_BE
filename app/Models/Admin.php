<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'admin';        // Tên bảng
    protected $primaryKey = 'maAdmin'; // Khóa chính
    public $incrementing = true;       // vì là số nguyên tự tăng
    protected $keyType = 'int';

    public $timestamps = true;         // Có timestamps

    protected $fillable = ['maAdmin', 'matKhau', 'email', 'hoTen'];

 public function hoSos()
{
    return $this->belongsToMany(HoSo::class, 'admin_ho_so', 'maAdmin', 'maHS');
}

public function taskComments()
{
    return $this->morphMany(TaskComment::class, 'user');
}


}
