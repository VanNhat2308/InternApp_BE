<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DuLieuMauSeeder extends Seeder
{
    public function run()
    {
        // 1. Trường
        DB::table('truongs')->insert([
            ['maTruong' => 'T01', 'tenTruong' => 'Trường CNTT HCM', 'moTa' => 'Trường đào tạo công nghệ thông tin'],
            ['maTruong' => 'T02', 'tenTruong' => 'Trường Kinh tế HN', 'moTa' => 'Trường chuyên về kinh tế và quản trị'],
        ]);

        // 2. Sinh viên
        DB::table('sinh_viens')->insert([
            [
                'maSV' => "SV001",
                'hoTen' => 'Nguyễn Văn A',
                'gioiTinh' => 'Nam',
                'ngaySinh' => '2002-05-20',
                'email' => 'a@gmail.com',
                'soDienThoai' => '0123456789',
                'diaChi' => 'TP.HCM',
                'nganh' => 'Công nghệ thông tin',
                'tenDangNhap' => 'nguyenvana',
                'password' => bcrypt('123456'),
                'duLieuKhuonMat' => 'face_data_1',
                'soDTGV' => '0909090909',
                'tenGiangVien' => 'Thầy Minh',
                'thoiGianTT' => '2024-01-01',
                'viTri' => 'Dev Backend',
                'maTruong' => 'T01',
            ],
            [
                'maSV' =>"SV002",
                'hoTen' => 'Trần Thị B',
                'gioiTinh' => 'Nữ',
                'ngaySinh' => '2001-10-10',
                'email' => 'b@gmail.com',
                'soDienThoai' => '0987654321',
                'diaChi' => 'Hà Nội',
                'nganh' => 'Kế toán',
                'tenDangNhap' => 'tranthib',
                'password' => bcrypt('abcdef'),
                'duLieuKhuonMat' => 'face_data_2',
                'soDTGV' => '0911223344',
                'tenGiangVien' => 'Cô Lan',
                'thoiGianTT' => '2024-02-15',
                'viTri' => 'Thực tập viên tài chính',
                'maTruong' => 'T02',
            ]
        ]);

        // 3. Admin
        DB::table('admin')->insert([
            ['maAdmin' => 'AD01', 'hoTen' => 'Admin Chính', 'email' => 'admin1@gmail.com', 'matKhau' => bcrypt('admin123')],
            ['maAdmin' => 'AD02', 'hoTen' => 'Admin Phụ', 'email' => 'admin2@gmail.com', 'matKhau' => bcrypt('admin456')],
        ]);

        // 4. Hồ sơ
        DB::table('ho_sos')->insert([
            ['maHS' => 'HS001', 'maSV' => "SV001", 'ngayNop' => '2024-03-01', 'trangThai' => 'Chờ duyệt'],
            ['maHS' => 'HS002', 'maSV' => "SV002", 'ngayNop' => '2024-03-02', 'trangThai' => 'Đã duyệt'],
        ]);

        // 5. admin_ho_so (pivot)
        DB::table('admin_ho_so')->insert([
            ['maAdmin' => 'AD01', 'maHS' => 'HS001'],
            ['maAdmin' => 'AD02', 'maHS' => 'HS001'],
            ['maAdmin' => 'AD01', 'maHS' => 'HS002'],
        ]);

        // 6. Lịch
        DB::table('lichs')->insert([
            ['maLich' => 1, 'maSV' =>"SV001", 'ngay' => '2024-04-01', 'noiDung' => 'Học ReactJS', 'trangThai' => 'Hoàn thành'],
            ['maLich' => 2, 'maSV' => "SV001", 'ngay' => '2024-04-03', 'noiDung' => 'Học Laravel', 'trangThai' => 'Chưa hoàn thành'],
            ['maLich' => 3, 'maSV' =>"SV002", 'ngay' => '2024-04-05', 'noiDung' => 'Thực tập tài chính', 'trangThai' => 'Hoàn thành'],
        ]);

        // 7. Nhật ký
        DB::table('nhat_kies')->insert([
            ['maNK' => 1, 'maSV' => "SV001", 'ngayTao' => '2024-04-10', 'noiDung' => 'Hôm nay học Laravel cơ bản', 'trangThai' => 'Hoạt động'],
            ['maNK' => 2, 'maSV' => "SV002", 'ngayTao' => '2024-04-11', 'noiDung' => 'Làm báo cáo tài chính tuần', 'trangThai' => 'Hoạt động'],
        ]);

        // 8. Báo cáo
        DB::table('bao_caos')->insert([
            ['maBC' => 1, 'maSV' => "SV001", 'loai' => 'Báo cáo tuần 1', 'ngayTao' => '2024-04-15', 'noiDung' => 'Hoàn thành layout dự án'],
            ['maBC' => 2, 'maSV' => "SV002", 'loai' => 'Báo cáo tuần 1', 'ngayTao' => '2024-04-16', 'noiDung' => 'Tổng hợp báo cáo chi tiêu thực tập'],
        ]);
    }
}
