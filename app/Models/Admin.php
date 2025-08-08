<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Admin extends Authenticatable implements JWTSubject
{
    protected $table = 'admin';        // Tên bảng
    protected $primaryKey = 'maAdmin'; // Khóa chính
    public $incrementing = true;       // vì là số nguyên tự tăng
    protected $keyType = 'int';

    public $timestamps = true;         // Có timestamps

    protected $fillable = ['maAdmin', 'password', 'email', 'hoTen'];
    protected $hidden = [
    'matKhau', 'password', 'remember_token',];
    


    public function getAuthPassword()
{
    return $this->password;
}

public function getJWTIdentifier() {
        return $this->getKey();
}

public function getJWTCustomClaims() {
        return ['role' => 'admin'];
}

 public function hoSos()
{
    return $this->belongsToMany(HoSo::class, 'admin_ho_so', 'maAdmin', 'maHS');
}

public function taskComments()
{
    return $this->morphMany(TaskComment::class, 'user');
}

public function notifications()
{
    return $this->morphMany(Notification::class, 'notifiable');
}

}
