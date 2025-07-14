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
            ['maTruong' => 'VLU', 'tenTruong' => 'VLU', 'moTa' => 'Trường đào tạo công nghệ thông tin'],
            ['maTruong' => 'UEF', 'tenTruong' => 'UEF', 'moTa' => 'Trường chuyên về kinh tế và quản trị'],
            ['maTruong' => 'HSU', 'tenTruong' => 'HSU', 'moTa' => 'Trường chuyên về kinh tế và quản trị'],
            ['maTruong' => 'UEH', 'tenTruong' => 'UEH', 'moTa' => 'Trường chuyên về kinh tế và quản trị'],
            ['maTruong' => 'UEL', 'tenTruong' => 'UEL', 'moTa' => 'Trường chuyên về kinh tế và quản trị'],
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
                'maTruong' => collect(['UEH', 'VLU'])->random(),
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
                'maLich' => 'LICH' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'maSV' => $id, // hoặc random trong danh sách sinh viên
                'ngay' => now()->startOfWeek()->addDays($i)->toDateString(), // phân bổ đều các ngày
                'time' => $i % 2 == 0 ? '08:00' : '13:00',
                'duration' => 4,
                'noiDung' => "Lịch làm việc ngày " . ($i),
                'trangThai' => $i % 2 == 0 ? 'Đã học' : 'Đã nghỉ',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 6. Báo cáo
            DB::table('bao_caos')->insert([
                'maBC' => $i,
                'maSV' => $id,
                'loai' => "Báo cáo tuần " . $i,
                'ngayTao' => now()->addDays($i)->toDateString(),
                'noiDung' => str_repeat("Đây là nội dung chi tiết của báo cáo tuần $i. ", 50), // khoảng 2000+ ký tự
                'tepDinhKem' => 'baocaos/26017-68701-1-PB.pdf',
            ]);

            // 7. Task
            DB::table('tasks')->insert([
                'maSV' => $id,
                'tieuDe' => 'Task ' . ($i),
                'noiDung' => 'Nội dung task ' . ($i),
                'diemSo' => null,
                'doUuTien' => collect(['Cao', 'Trung bình', 'Thấp'])->random(),
                'hanHoanThanh' => now()->addDays($i + 7)->toDateString(),
                'trangThai' => collect(['Đã nộp', 'Chưa nộp', 'Nộp trễ'])->random(),
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

        // Điểm danh thêm cho sinh viên có maSV = 1 trong 7 ngày gần đây
        $maSV = 1;
        for ($j = 0; $j < 7; $j++) {
            $date = now()->subDays($j);
            $randomHour = rand(7, 9);
            $randomMinute = rand(0, 59);
            $gioBatDau = sprintf('%02d:%02d:00', $randomHour, $randomMinute);
            $gioKetThuc = '10:00:00';
            $trangThai = strtotime($gioBatDau) > strtotime('08:00:00') ? 'late' : 'on_time';

            DB::table('diem_danhs')->insert([
                'maSV' => $maSV,
                'ngay_diem_danh' => $date->toDateString(),
                'gio_bat_dau' => $gioBatDau,
                'gio_ket_thuc' => $gioKetThuc,
                'trang_thai' => $trangThai,
                'ghi_chu' => $trangThai === 'late' ? 'Đến trễ so với giờ quy định' : 'Có mặt đúng giờ',
            ]);
        }

    DB::table('admin')->insert([
    'maAdmin' => 1,
    'matKhau' => bcrypt('admin123'), // nhớ dùng bcrypt nếu bạn đang login bằng Laravel Auth
    'email' => 'admin@example.com',
    'hoTen' => 'Admin Chính',
    'created_at' => now(),
    'updated_at' => now(),
]);


     $adminId = 1; // Giả sử admin có id = 1

for ($sinhvienId = 1; $sinhvienId <= 10; $sinhvienId++) {
    // Tạo cuộc hội thoại
    $conversationId = DB::table('conversations')->insertGetId([
        'user1_role' => 'sinhvien',
        'user1_id' => $sinhvienId,
        'user2_role' => 'admin',
        'user2_id' => $adminId,
        'updated_at' => now(),
    ]);

    $now = now();

    // 1. Sinh viên gửi tin nhắn đầu tiên
    $message1 = DB::table('messages')->insertGetId([
        'from_role' => 'sinhvien',
        'from_id' => $sinhvienId,
        'to_role' => 'admin',
        'to_id' => $adminId,
        'conversation_id' => $conversationId,
        'content' => "Thầy ơi em là sinh viên $sinhvienId có vài thắc mắc về task.",
        'type' => 'text',
        'is_read' => false,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    // 2. Admin trả lời
    $message2 = DB::table('messages')->insertGetId([
        'from_role' => 'admin',
        'from_id' => $adminId,
        'to_role' => 'sinhvien',
        'to_id' => $sinhvienId,
        'conversation_id' => $conversationId,
        'content' => "Chào em sinh viên $sinhvienId, thầy đã nhận được tin nhắn.",
        'type' => 'text',
        'is_read' => false,
        'created_at' => $now->copy()->addMinutes(2),
        'updated_at' => $now->copy()->addMinutes(2),
    ]);

    // 3. Gửi thêm tin nhắn mô phỏng hội thoại
    for ($j = 1; $j <= 2; $j++) {
        DB::table('messages')->insert([
            'from_role' => 'sinhvien',
            'from_id' => $sinhvienId,
            'to_role' => 'admin',
            'to_id' => $adminId,
            'conversation_id' => $conversationId,
            'content' => "Em đang làm phần $j của task ạ.",
            'type' => 'text',
            'is_read' => false,
            'created_at' => $now->copy()->addMinutes(3 + $j),
            'updated_at' => $now->copy()->addMinutes(3 + $j),
        ]);

        DB::table('messages')->insert([
            'from_role' => 'admin',
            'from_id' => $adminId,
            'to_role' => 'sinhvien',
            'to_id' => $sinhvienId,
            'conversation_id' => $conversationId,
            'content' => "Em cứ tiếp tục nhé, thầy đang theo dõi.",
            'type' => 'text',
            'is_read' => false,
            'created_at' => $now->copy()->addMinutes(5 + $j),
            'updated_at' => $now->copy()->addMinutes(5 + $j),
        ]);
    }

    // 4. Cập nhật last_message_id cho cuộc trò chuyện
    DB::table('conversations')->where('id', $conversationId)->update([
        'last_message_id' => $message2,
        'updated_at' => now(),
    ]);

    // 5. Đính kèm file cho message đầu tiên
    DB::table('attachments')->insert([
        'message_id' => $message1,
        'file_url' => 'cvs/7D5GAiOG13sWOkWUdnJUUgNCnb7sXbkivxLYLTEE.pdf',
        'file_type' => 'pdf',
        'uploaded_at' => now(),
    ]);
}


            
    
}
}