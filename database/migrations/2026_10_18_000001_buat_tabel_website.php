<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('aktif')->default(true)->after('password');
            $table->text('app_authentication_secret')->nullable();
            $table->text('app_authentication_recovery_codes')->nullable();
        });

        Schema::create('pengaturan', function (Blueprint $table) {
            $table->string('kunci', 100)->primary();
            $table->longText('nilai')->nullable();
        });

        // Halaman berisi teks tetap: sejarah, visi-misi, struktur organisasi, kebijakan privasi, dst.
        Schema::create('halaman', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 150);
            $table->string('slug', 160)->unique();
            $table->string('grup', 20)->default('profil');
            $table->string('ringkasan', 300)->nullable();
            $table->longText('isi')->nullable();
            $table->string('gambar')->nullable();
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->boolean('terbit')->default(true);
            $table->timestamps();
        });

        Schema::create('berita', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 200);
            $table->string('slug', 220)->unique();
            $table->string('kategori', 20)->default('kegiatan');
            $table->string('ringkasan', 300)->nullable();
            $table->longText('isi')->nullable();
            $table->string('gambar')->nullable();
            $table->string('keterangan_gambar', 200)->nullable();
            // Nama penulis bila tulisan berasal dari guru lain; kosong berarti "Admin Sekolah"
            $table->string('penulis', 120)->nullable();
            $table->string('status', 10)->default('draf');
            $table->dateTime('terbit_pada')->nullable();
            $table->boolean('disematkan')->default(false);
            $table->date('sematkan_sampai')->nullable();
            $table->boolean('foto_diperiksa')->default(false);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->index(['status', 'terbit_pada']);
        });

        Schema::create('agenda', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 200);
            $table->date('mulai');
            $table->date('selesai')->nullable();
            $table->string('waktu', 60)->nullable();
            $table->string('tempat', 150)->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('libur')->default(false);
            $table->timestamps();
            $table->index('mulai');
        });

        Schema::create('album', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 200);
            $table->string('slug', 220)->unique();
            $table->date('tanggal')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('video_youtube')->nullable();
            $table->boolean('terbit')->default(true);
            $table->boolean('foto_diperiksa')->default(false);
            $table->timestamps();
        });

        Schema::create('foto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('album_id')->constrained('album')->restrictOnDelete();
            $table->string('path');
            $table->string('keterangan', 200)->nullable();
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('prestasi', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 200);
            $table->string('peraih', 200);
            $table->string('jenis_peraih', 10)->default('murid');
            $table->string('peringkat', 60)->nullable();
            $table->string('tingkat', 20)->default('kota');
            $table->string('penyelenggara', 150)->nullable();
            $table->date('tanggal')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('foto')->nullable();
            $table->boolean('terbit')->default(true);
            $table->boolean('foto_diperiksa')->default(false);
            $table->timestamps();
        });

        Schema::create('guru', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->string('slug', 170)->unique();
            $table->string('jabatan', 150)->nullable();
            $table->string('kelompok', 20)->default('guru');
            $table->string('nip', 30)->nullable();
            $table->string('motto', 200)->nullable();
            $table->text('profil')->nullable();
            $table->string('foto')->nullable();
            $table->json('media_sosial')->nullable();
            $table->json('tautan')->nullable();
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->boolean('tampil')->default(true);
            $table->timestamps();
        });

        Schema::create('unduhan', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 200);
            $table->string('kategori', 60)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('berkas');
            $table->string('nama_berkas')->nullable();
            $table->unsignedInteger('ukuran')->nullable();
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->boolean('terbit')->default(true);
            $table->timestamps();
        });

        Schema::create('pesan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 120)->nullable();
            $table->string('kontak', 120)->nullable();
            $table->string('kategori', 20);
            $table->text('isi');
            $table->string('status', 10)->default('baru');
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->index('status');
        });

        Schema::create('catatan_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('aksi', 30);
            $table->string('jenis', 40);
            $table->unsignedBigInteger('subjek_id')->nullable();
            $table->string('judul', 220)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        foreach (['catatan_aktivitas', 'pesan', 'unduhan', 'guru', 'prestasi', 'foto', 'album', 'agenda', 'berita', 'halaman', 'pengaturan'] as $tabel) {
            Schema::dropIfExists($tabel);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['aktif', 'app_authentication_secret', 'app_authentication_recovery_codes']);
        });
    }
};
