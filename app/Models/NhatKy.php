<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NhatKy extends Model
{
      use HasFactory;

    protected $table = 'nhat_kies';
    protected $primaryKey = 'maNK';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'maNK',
        'ngayTao',
        'noiDung',
        'trangThai',
        'maSV',
    ];

    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class, 'maSV', 'maSV');
    }
}
