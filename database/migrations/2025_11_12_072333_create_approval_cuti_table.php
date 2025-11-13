<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_cuti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuan_cuti')->onDelete('cascade');
            $table->foreignId('approver_id')->constrained('users')->onDelete('cascade');
            $table->integer('level_approval')->comment('1, 2, 3... urutan approval');
            $table->enum('status_approval', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->text('catatan')->nullable()->comment('Catatan approver');
            $table->text('catatan_reject')->nullable()->comment('Alasan penolakan');
            $table->timestamp('tanggal_approval')->nullable();
            $table->timestamp('notified_at')->nullable()->comment('Kapan notifikasi dikirim');
            $table->timestamps();

            $table->index(['pengajuan_id', 'level_approval']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_cuti');
    }
};
