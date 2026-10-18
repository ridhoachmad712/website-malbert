<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Foto sampul pilihan admin; bila kosong (atau fotonya dihapus), sampul memakai foto pertama album
        Schema::table('album', function (Blueprint $table) {
            $table->foreignId('sampul_foto_id')->nullable()->after('video_youtube')->constrained('foto')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('album', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sampul_foto_id');
        });
    }
};
