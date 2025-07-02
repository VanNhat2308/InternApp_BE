<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietNhatKy extends Model
{
    protected $fillable = ['tenCongViec', 'ketQua', 'tienDo', 'maNK'];

    public function nhatKy()
    {
        return $this->belongsTo(NhatKy::class, 'maNK', 'maNK');
    }
}
