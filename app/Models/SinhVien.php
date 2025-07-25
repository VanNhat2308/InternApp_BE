<?php

namespace App\Models;
use App\Models\Task;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SinhVien extends Authenticatable implements JWTSubject
{
     use HasFactory;

    protected $table = 'sinh_viens';
    protected $primaryKey = 'maSV';
    protected $keyType = 'string';

    protected $fillable = [
        'maSV',
        'trangThai',
        'tenDangNhap',
        'password',
        'hoTen',
        'email',
        'soDienThoai',
        'diaChi',
        'ngaySinh',
        'gioiTinh',
        'nganh',
        'duLieuKhuonMat',
        'cV',
        'soDTGV',
        'tenGiangVien',
        'thoiGianTT',
        'viTri',
        'kyThucTap',
        'maTruong',
    ];
    protected $hidden = [
     'password', 'remember_token',
];

  public function getAuthIdentifierName()
    {
        return 'tenDangNhap';
    }

    // Laravel mặc định dùng cột `password`, nên override:
public function getAuthPassword()
{
    return $this->password; 
}


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return ['role' => 'sinhvien'];
    }
    public function truong()
    {
        return $this->belongsTo(Truong::class, 'maTruong', 'maTruong');
    }
    public function hoSo()
    {
        return $this->hasOne(HoSo::class, 'maSV', 'maSV');
     }
     public function liches()
{
    return $this->hasMany(Lich::class, 'maSV', 'maSV');
}
public function nhatKy()
{
    return $this->hasMany(NhatKy::class, 'maSV', 'maSV');
}
public function baoCao()
{
    return $this->hasOne(BaoCao::class, 'maSV', 'maSV');
}
 public function sinhViens()
{
    return $this->belongsToMany(SinhVien::class, 'sinh_vien_task', 'id', 'maSV');
}


public function diemDanhs()
{
    return $this->hasMany(DiemDanh::class, 'maSV', 'maSV');
}

public function taskComments()
{
    return $this->morphMany(TaskComment::class, 'user');
}
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'from_id')->where('from_role', 'sinhvien');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'to_id')->where('to_role', 'sinhvien');
    }

}
