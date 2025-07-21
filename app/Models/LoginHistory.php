<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    protected $table = 'login_histories';

    protected $fillable = [
        'email',
        'loaiNguoiDung',
        'ip_address',
    ];
    public $timestamps = false;


}
