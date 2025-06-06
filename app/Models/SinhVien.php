<?php

namespace App\Models;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SinhVien extends Model
{
     use HasFactory;

    protected $table = 'sinh_viens';
    protected $primaryKey = 'maSV';
    protected $keyType = 'string';

    protected $fillable = [
        'maSV',
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
        'maTruong',
    ];

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
    return $this->hasOne(NhatKy::class, 'maSV', 'maSV');
}
public function baoCao()
{
    return $this->hasOne(BaoCao::class, 'maSV', 'maSV');
}
public function tasks()
{
    return $this->hasMany(Task::class, 'maSV');
}

public function diemDanhs()
{
    return $this->hasMany(DiemDanh::class, 'maSV', 'maSV');
}



}
