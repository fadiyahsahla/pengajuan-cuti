<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_cuti', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jenis', 100);
            $table->text('keterangan')->nullable();
            $table->integer('min_hari_pengajuan')->default(0)->comment('Minimum hari sebelum cuti, 0=tidak ada minimum');
            $table->boolean('perlu_dokumen')->default(false)->comment('Apakah wajib upload dokumen');
            $table->enum('prioritas', ['normal', 'cepat'])->default('normal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_cuti');
    }
};
