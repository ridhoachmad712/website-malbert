<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Dokumen transparansi anggaran (RKAS, laporan realisasi BOSP, dst.) yang wajib dipublikasikan sekolah
        Schema::create('dokumen_anggaran', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('tahun');
            $table->string('jenis', 30);
            $table->string('sumber_dana', 30)->default('bosp_reguler');
            $table->string('judul', 200);
            $table->string('periode', 60)->nullable();
            $table->date('tanggal')->nullable();
            $table->unsignedBigInteger('penerimaan')->nullable();
            $table->unsignedBigInteger('belanja')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('berkas');
            $table->string('nama_berkas')->nullable();
            $table->unsignedInteger('ukuran')->nullable();
            $table->boolean('terbit')->default(true);
            $table->timestamps();
            $table->index(['tahun', 'jenis']);
        });

        Schema::create('ekstrakurikuler', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 120);
            $table->string('slug', 140)->unique();
            $table->string('ringkasan', 300)->nullable();
            $table->longText('deskripsi')->nullable();
            $table->string('jadwal', 120)->nullable();
            $table->string('tempat', 120)->nullable();
            $table->string('pembina', 150)->nullable();
            $table->string('peserta', 120)->nullable();
            $table->string('foto')->nullable();
            $table->foreignId('album_id')->nullable()->constrained('album')->nullOnDelete();
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->boolean('tampil')->default(true);
            $table->boolean('foto_diperiksa')->default(false);
            $table->timestamps();
        });

        Schema::create('sarana', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 120);
            $table->string('jumlah', 60)->nullable();
            $table->text('keterangan')->nullable();
            $table->json('foto')->nullable();
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->boolean('tampil')->default(true);
            $table->timestamps();
        });

        // Alumni: cerita yang ditulis admin, atau data yang dikirim alumni lewat formulir lalu diperiksa admin
        Schema::create('alumni', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->unsignedSmallInteger('tahun_lulus')->nullable();
            $table->string('kegiatan', 200)->nullable();
            $table->text('cerita')->nullable();
            $table->string('kontak', 120)->nullable();
            $table->string('foto')->nullable();
            $table->string('sumber', 10)->default('admin');
            $table->string('status', 15)->default('menunggu');
            $table->boolean('setuju_tampil')->default(false);
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();
            $table->index('status');
        });
    }

    public function down(): void
    {
        foreach (['alumni', 'sarana', 'ekstrakurikuler', 'dokumen_anggaran'] as $tabel) {
            Schema::dropIfExists($tabel);
        }
    }
};
