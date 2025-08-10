<?php
use App\Models\Admin;
use App\Models\SinhVien;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('admin.{id}', function ($user, $id) {
    return (int) $user->maAdmin === (int) $id;
});


Broadcast::channel('sinhvien.{id}', function ($user, $id) {
    return (int) $user->maSV === (int) $id;
});