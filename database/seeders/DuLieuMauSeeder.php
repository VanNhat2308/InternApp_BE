<?php

namespace Database\Seeders;

use App\Models\DiemDanh;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DuLieuMauSeeder extends Seeder
{
    public function run()
    {
        // 1. Thêm trường
        DB::table('truongs')->insert([
            ['maTruong' => 'T01', 'tenTruong' => 'VLU', 'moTa' => 'Trường đào tạo công nghệ thông tin'],
            ['maTruong' => 'T02', 'tenTruong' => 'UEF', 'moTa' => 'Trường chuyên về kinh tế và quản trị'],
            ['maTruong' => 'T03', 'tenTruong' => 'HSU', 'moTa' => 'Trường chuyên về kinh tế và quản trị'],
            ['maTruong' => 'T04', 'tenTruong' => 'UEH', 'moTa' => 'Trường chuyên về kinh tế và quản trị'],
            ['maTruong' => 'T05', 'tenTruong' => 'UEL', 'moTa' => 'Trường chuyên về kinh tế và quản trị'],
        ]);

        // 2. Tạo 100 sinh viên và các dữ liệu liên quan
   for ($i = 1; $i <= 100; $i++) {
    $id = DB::table('sinh_viens')->insertGetId([
        'hoTen' => 'Sinh viên ' . $i,
        'gioiTinh' => $i % 2 == 0 ? 'Nam' : 'Nữ',
        'ngaySinh' => now()->subYears(rand(18, 24))->toDateString(),
        'email' => "sv{$i}@example.com",
        'soDienThoai' => '09' . rand(10000000, 99999999),
        'diaChi' => 'Địa chỉ ' . $i,
        'nganh' => 'Ngành ' . rand(1, 5),
        'tenDangNhap' => "sinhvien{$i}",
        'password' => bcrypt('matkhau123'),
        'duLieuKhuonMat' => "face_data_{$i}",
        'soDTGV' => '09' . rand(10000000, 99999999),
        'tenGiangVien' => 'GV ' . $i,
        'thoiGianTT' => '3 tháng',
        'viTri' => collect([
            'Front-end Developer', 
            'Back-end Developer', 
            'Fullstack', 
            'Tester',
            'Graphic Design',
            'Business analyst',
            'Digital Marketing'
        ])->random(),
        'maTruong' => collect(['T01','T02','T03','T04','T05'])->random(),
        'kyThucTap' => collect([
            'HK1 2023-2024',
            'HK2 2023-2024',
            'HK1 2024-2025',
            'HK2 2024-2025'
        ])->random(),
    ]);



            // 3. Hồ sơ
            DB::table('ho_sos')->insert([
                'maHS' => 'HS' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'maSV' => $id,
                'ngayNop' => now()->toDateString(),
                'trangThai' => $i % 2 == 0 ? 'Chờ duyệt' : 'Đã duyệt',
            ]);

            // 4. Nhật ký
            DB::table('nhat_kies')->insert([
                'maNK' => $i,
                'maSV' => $id,
                'ngayTao' => now()->subDays($i)->toDateString(),
                'noiDung' => "Nội dung nhật ký ngày " . ($i),
                'trangThai' => 'Hoạt động',
            ]);

            // 5. Lịch
            DB::table('lichs')->insert([
                'maLich' => $i,
                'maSV' => $id,
                'ngay' => now()->addDays($i)->toDateString(),
                'noiDung' => "Lịch làm việc ngày " . ($i),
                'trangThai' => $i % 2 == 0 ? 'Hoàn thành' : 'Chưa hoàn thành',
            ]);

            // 6. Báo cáo
            DB::table('bao_caos')->insert([
                'maBC' => $i,
                'maSV' => $id,
                'loai' => "Báo cáo tuần " . ($i),
                'ngayTao' => now()->addDays($i)->toDateString(),
                'noiDung' => "Nội dung báo cáo tuần " . ($i),
            ]);

            // 7. Task
            DB::table('tasks')->insert([
                'maSV' => $id,
                'tieuDe' => 'Task ' . ($i),
                'noiDung' => 'Nội dung task ' . ($i),
                'hanHoanThanh' => now()->addDays($i + 7)->toDateString(),
                'trangThai' => 'Chưa hoàn thành',
            ]);

            // 8. Điểm danh hôm qua và hôm nay
    foreach ([now()->subDay(), now()] as $day) {
    // Random giờ bắt đầu từ 07:30 đến 09:00
    $randomHour = rand(7, 9);
    $randomMinute = rand(0, 59);
    $gioBatDau = sprintf('%02d:%02d:00', $randomHour, $randomMinute);
    $gioKetThuc = '10:00:00';

    // So sánh với mốc 08:00:00
    $trangThai = strtotime($gioBatDau) > strtotime('08:00:00') ? 'late' : 'on_time';

    DB::table('diem_danhs')->insert([
        'maSV' => $id,
        'ngay_diem_danh' => $day->toDateString(),
        'gio_bat_dau' => $gioBatDau,
        'gio_ket_thuc' => $gioKetThuc,
        'trang_thai' => $trangThai,
        'ghi_chu' => $trangThai === 'late' ? 'Đến trễ so với giờ quy định' : 'Có mặt đúng giờ',
    ]);
}
        }
    }
}
