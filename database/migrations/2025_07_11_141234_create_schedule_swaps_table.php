<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
    {
        Schema::create('schedule_swaps', function (Blueprint $table) {
            $table->id();

            // Sinh viên (có thể dùng user_id hoặc ma_sv)
            $table->unsignedBigInteger('maSV'); // Foreign key
            // Hoặc dùng string nếu mã SV không phải ID
            // $table->string('ma_sv');

            // Thông tin ca hiện tại
            $table->date('old_date');
            $table->string('old_shift'); // vd: '8:00-12:00'

            // Thông tin ca muốn đổi sang
            $table->date('new_date')->nullable();
            $table->string('new_shift')->nullable();

            // Hình thức: 'doi' hoặc 'nghi'
            $table->enum('change_type', ['doi', 'nghi']);

            // Lý do
            $table->text('reason');

            // Trạng thái xử lý: pending / approved / rejected
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            // Ghi chú nếu có (admin xử lý)
            $table->text('admin_note')->nullable();

            $table->timestamps();

            // Foreign key (nếu dùng student_id)
            $table->foreign('maSV')->references('maSV')->on('sinh_viens')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_swaps');
    }
};
