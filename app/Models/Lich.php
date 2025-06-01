<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lich extends Model
{
      use HasFactory;

    protected $table = 'lichs';        // Tên bảng
    protected $primaryKey = 'maLich';  // Khóa chính
    public $incrementing = false;      // Vì khóa chính là string
    protected $keyType = 'string';

    protected $fillable = [
        'maLich',
        'ngay',
        'noiDung',
        'trangThai',
        'maSV',
    ];

    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class, 'maSV', 'maSV');
    }
}
