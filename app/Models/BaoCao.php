<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BaoCao extends Model
{
    use HasFactory;

    protected $table = 'bao_caos';
    protected $primaryKey = 'maBC';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'maBC',
        'loai',
        'ngayTao',
        'noiDung',
        'maSV',
    ];

    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class, 'maSV', 'maSV');
    }
}
