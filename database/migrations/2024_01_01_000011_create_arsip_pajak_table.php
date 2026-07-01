<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arsip_pajak', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('jenis_pajak_id')->constrained('jenis_pajak')->onDelete('cascade');
            $table->string('nomor_arsip')->unique();
            $table->year('tahun_pajak');
            $table->enum('periode', ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember','Triwulan I','Triwulan II','Triwulan III','Triwulan IV','Tahunan']);
            $table->decimal('jumlah_pajak', 18, 2);
            $table->date('tanggal_setor')->nullable();
            $table->string('nomor_bukti_setor')->nullable();
            $table->string('file_dokumen')->nullable();
            $table->string('nomor_ktp', 16)->nullable();
            $table->string('nomor_npwp', 20)->nullable();
            $table->string('file_ktp')->nullable();
            $table->string('file_npwp')->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['draft', 'menunggu', 'disetujui', 'ditolak'])->default('draft');
            $table->text('catatan_pimpinan')->nullable();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('tanggal_disetujui')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arsip_pajak');
    }
};
