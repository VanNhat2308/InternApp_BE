<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoSo extends Model
{
   use HasFactory;

    protected $table = 'ho_sos';
    protected $primaryKey = 'maHS';
    public $incrementing = false; // vì khóa chính là string
    protected $keyType = 'string';

    protected $fillable = [
        'maHS',
        'maSV',
        'ngayNop',
        'trangThai',
    ];

    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class, 'maSV', 'maSV');
    }

    public function admins()
    {
        return $this->belongsToMany(Admin::class, 'admin_ho_so', 'maHS', 'maAdmin');
    }
}
