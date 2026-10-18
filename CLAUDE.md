# Website resmi SDI Malbert: panduan pengembangan

Website resmi sekolah, terpisah dari aplikasi TEBAK AKSI (kode, database, dan akun admin sendiri). Kebutuhan dan keputusan produk ada di `KEBUTUHAN-WEBSITE.md`; ikuti dokumen itu.

## Prinsip yang wajib dijaga

- **Website tidak menyimpan dan tidak menampilkan data murid.** Satu-satunya pengecualian: nama peraih di Prestasi, yang diisi dan diperiksa admin.
- **Semua foto melewati pemeriksaan admin**: centang "foto sudah diperiksa" wajib sebelum berita bergambar, album, atau prestasi bergambar tampil. Foto selalu diunggah lewat `Isian::foto()` (dikecilkan, EXIF dibuang). Editor teks tidak mengizinkan gambar di dalam isi.
- **Isi dari editor selalu dibersihkan** saat ditampilkan (`<x-isi>`, memakai `Str::sanitizeHtml`). Jangan pernah `{!! $isi !!}` mentah.
- Hubungan dengan TEBAK AKSI hanya tautan. Jangan membaca database TEBAK AKSI.
- Pendaftaran murid baru lewat portal dinas; website tidak punya formulir pendaftaran.
- Seluruh antarmuka, nama tabel, model, dan variabel domain memakai **bahasa Indonesia**.
- Data contoh di seeder/tes selalu rekaan; folder `Docs/` tidak masuk git.
- **Tampilan mengikuti `DESIGN.md`**: tanpa emoji, gradasi, blur, atau bayangan pada panel; satu warna aksen (palet dipilih admin) untuk tindakan utama; keadaan kosong selalu menyebut sebab dan langkah berikutnya.

## Struktur

- `app/Models/Pengaturan.php`: semua isian tunggal (identitas, kepala sekolah, beranda, TEBAK AKSI, SPMB); nilai bawaan di `BAWAAN`.
- `app/Filament/Support/HalamanPengaturan.php`: dasar halaman admin yang mengisi Pengaturan (IdentitasSekolah, TampilanBeranda, ProgramTebakAksi, Spmb).
- `app/Filament/Support/HapusAman.php`: penghapusan yang menolak album berisi foto dan akun admin sendiri.
- `app/Models/Concerns`: `PunyaSlug` (slug otomatis, tidak berubah saat judul diubah), `HapusBerkas` (berkas lama ikut dihapus), `CatatAktivitas`.
- `app/Http/Controllers/PublikController.php` + `resources/views/publik/*`: halaman publik (Blade biasa, tanpa Livewire).
- `app/Http/Middleware/HeaderKeamanan.php`: header keamanan; CSP ketat di halaman publik (skrip hanya dari website sendiri, sematan hanya Google Maps dan YouTube).
- `scripts/pasang-public-html.sh` dan `scripts/cadangan-harian.sh`: pemasangan web root dan cadangan harian di Hostinger.

## Catatan teknis

- Laravel 13, Filament 5, Tailwind 4. Kelas CSS bersama (`kartu`, `tombol-utama`, `isian`, `lencana`, dst.) ada di `resources/css/app.css`.
- Jangan menulis skrip sebaris di halaman publik (CSP menolaknya); taruh di `resources/js/app.js`.
- Closure Filament diinjeksi berdasarkan nama parameter: pakai `$query`, `$record`, `$data`, `$get`.
- `public/build` ikut di-commit karena hosting tidak menjalankan Node; jalankan `npm run build` sebelum commit perubahan tampilan.
- Di server, `exec()` dan `symlink()` PHP dimatikan: symlink dibuat lewat skrip shell, cadangan otomatis lewat Cron hPanel.
- `public_html` berisi symlink `tebakaksi` milik TEBAK AKSI; jangan pernah mengganti seluruh `public_html`.
- Tes: `php artisan test` (SQLite in-memory).
