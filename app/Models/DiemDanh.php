<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiemDanh extends Model
{
      protected $fillable = [
        'maSV',
        'ngay_diem_danh',
        'gio_bat_dau',
        'gio_ket_thuc',
        'trang_thai',
        'ghi_chu',
    ];

    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class, 'maSV', 'maSV');
    }
}
