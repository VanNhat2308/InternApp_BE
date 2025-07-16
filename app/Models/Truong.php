<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Truong extends Model
{
    use HasFactory;

    protected $table = 'truongs';

    protected $fillable = [
        'maTruong',
        'tenTruong',
        'moTa',
        'logo'
    ];

    public function sinhViens()
    {
        return $this->hasMany(SinhVien::class, 'maTruong', 'maTruong');
    }
}
