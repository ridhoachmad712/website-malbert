# Website Resmi SDI Malbert

Website resmi UPT SPF SD Inpres Mallengkeri Bertingkat (sdimalbert.sch.id). Terpisah dari aplikasi TEBAK AKSI: kode, database, dan akun admin sendiri. Kebutuhan dan keputusan produk ada di `KEBUTUHAN-WEBSITE.md`.

- Halaman publik: Beranda, Profil (identitas, sambutan, halaman profil, guru dan tendik), Berita dan Pengumuman, Agenda, Galeri, Prestasi, Unduhan, Program TEBAK AKSI, SPMB, Kontak.
- Panel admin di `/admin` (Filament). Semua konten dikelola admin; tidak ada pendaftaran akun.

## Menjalankan di laptop

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan filament:assets
npm run build
php artisan serve
```

Seeder lokal membuat akun `admin@sdimalbert.test` dengan kata sandi `password` serta data contoh rekaan. Data contoh tidak pernah dibuat di server produksi.

Tes: `php artisan test`.

## Memasang di Hostinger

Susunan server: aplikasi di `~/website-sdimalbert`, web root domain utama di `~/domains/sdimalbert.sch.id/public_html`. Di dalam `public_html` ada symlink `tebakaksi` milik aplikasi TEBAK AKSI yang **tidak boleh** dihapus; skrip pemasangan sudah melindunginya.

1. **Buat database** di hPanel (Databases, MySQL): catat nama database, pengguna, dan kata sandi. Gunakan pengguna database yang berbeda dari TEBAK AKSI.
2. **Ambil kode** lewat SSH:
   ```bash
   cd ~
   git clone https://github.com/ridhoachmad712/website-sdimalbert.git
   cd website-sdimalbert
   composer install --no-dev --optimize-autoloader
   cp .env.example .env
   php artisan key:generate
   ```
3. **Isi `.env`**:
   ```
   APP_NAME="SDI Malbert"
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://sdimalbert.sch.id
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_DATABASE=...
   DB_USERNAME=...
   DB_PASSWORD=...
   ADMIN_EMAIL=surel-admin-sekolah
   ADMIN_PASSWORD=kata-sandi-awal-yang-kuat
   ```
4. **Siapkan database dan berkas**:
   ```bash
   php artisan migrate --force
   php artisan db:seed --class=DataAwalSeeder --force
   php artisan filament:assets
   php artisan optimize
   ```
5. **Hubungkan web root** (website lama dipindahkan ke `~/arsip-public_html-lama-TANGGAL`, tidak dihapus; folder aplikasi lama `~/domains/sdimalbert.sch.id/website` tidak disentuh):
   ```bash
   bash ~/website-sdimalbert/scripts/pasang-public-html.sh
   ```
   Skrip ini membuat symlink lewat shell, karena fungsi `symlink()` PHP dimatikan di Hostinger (jadi `php artisan storage:link` tidak dipakai di server).
6. **Periksa**: buka `https://sdimalbert.sch.id` dan `https://tebakaksi.sdimalbert.sch.id` (TEBAK AKSI harus tetap berjalan).
7. **SSL**: pastikan SSL aktif di hPanel (Security, SSL).
8. **Cadangan harian**: di hPanel, Advanced, Cron Jobs, tambahkan perintah berikut setiap hari pukul 02.00:
   ```
   bash /home/u272545584/website-sdimalbert/scripts/cadangan-harian.sh
   ```
   Cadangan disimpan di `~/cadangan-website` (14 terakhir). Admin juga bisa mengunduh cadangan dari menu Pengaturan, Cadangan Data.
9. **Masuk pertama kali** di `/admin`, ganti kata sandi di Profil, lalu aktifkan **verifikasi dua langkah** (aplikasi autentikator) di halaman yang sama. Hapus `ADMIN_PASSWORD` dari `.env` setelah itu.
10. **Isi data awal**: ikuti daftar "Lengkapi website" di Dasbor admin (logo, identitas, alamat, peta, kepala sekolah, visi-misi, guru).

### Memperbarui

```bash
cd ~/website-sdimalbert
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan filament:assets
php artisan optimize
bash scripts/pasang-public-html.sh
```

Aset CSS/JS dibangun di laptop (`npm run build`) dan ikut di-commit, karena server tidak menjalankan Node.
