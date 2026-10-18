# Analisa Kebutuhan Website Resmi SDI Malbert

UPT SPF SD Inpres Mallengkeri Bertingkat (SDI Malbert). Dokumen analisa, belum ada kode yang ditulis. Disusun 18 Oktober 2026.

Website resmi ini sistem baru, terpisah dari TEBAK AKSI: repo sendiri, database sendiri, akun admin sendiri, panel admin sendiri.

---

## 1. Tujuan dan pembaca

**Tujuan utama**

1. Menjadi sumber informasi resmi sekolah yang mudah ditemukan di Google (nama, alamat, NPSN, akreditasi, kontak).
2. Memberi kabar kegiatan dan prestasi secara rutin, sehingga orang tua dan masyarakat percaya pada sekolah.
3. Memudahkan pendaftaran murid baru (SPMB): jadwal, syarat, alur, dan tautan ke portal resmi dinas.
4. Menyediakan dokumen yang sering diminta (kalender pendidikan, brosur, formulir) tanpa orang tua harus datang ke sekolah.
5. Memperkenalkan program unggulan, termasuk TEBAK AKSI, kepada dinas dan sekolah lain.

**Pembaca dan yang mereka cari**

| Pembaca | Yang dicari | Halaman utama |
|---|---|---|
| Orang tua murid | Agenda, libur, pengumuman, kegiatan anak, kontak wali kelas lewat sekolah | Beranda, Pengumuman, Agenda, Galeri, Kontak |
| Calon orang tua murid | Gambaran sekolah, guru, fasilitas, biaya (gratis/tidak), cara mendaftar | Profil, Program, SPMB, Kontak/peta |
| Masyarakat sekitar | Alamat, jam layanan, kegiatan sekolah | Beranda, Kontak, Berita |
| Dinas Pendidikan dan pengawas | Identitas resmi, akreditasi, struktur organisasi, program, dokumentasi | Profil, Program unggulan, Prestasi, Unduhan |
| Alumni | Kabar sekolah, sejarah, foto | Sejarah, Berita, Galeri |
| Guru dan sekolah lain | Praktik baik, program literasi | Program unggulan, Berita |

Titik fokus Beranda (mengikuti prinsip satu titik fokus di DESIGN.md): **pengumuman/berita terbaru**. Saat masa SPMB, titik fokus berganti ke pengumuman SPMB.

---

## 2. Struktur halaman dan menu

Menu atas dibatasi 6 butir agar tetap ringkas di HP:

**Beranda · Profil · Berita · Program · SPMB · Kontak**

Galeri, Agenda, Prestasi, dan Unduhan ditaruh sebagai submenu atau tautan di Beranda dan kaki halaman.

| Halaman | Isi | Status |
|---|---|---|
| **Beranda** | Nama dan logo sekolah, sambutan singkat kepala sekolah, pengumuman terbaru, 3 berita terbaru, agenda terdekat, sorotan program, tautan cepat (SPMB, Unduhan, Kontak) | Wajib |
| **Profil → Identitas Sekolah** | NPSN, status, akreditasi, alamat, kurikulum, jumlah rombel (angka saja), tautan ke data resmi Kemendikdasmen | Wajib |
| **Profil → Sejarah** | Riwayat singkat sekolah, termasuk perubahan nama | Wajib |
| **Profil → Visi, Misi, Tujuan** | Teks | Wajib |
| **Profil → Sambutan Kepala Sekolah** | Foto kepala sekolah dan sambutan | Wajib |
| **Profil → Struktur Organisasi** | Bagan (gambar unggahan) atau daftar jabatan | Wajib |
| **Profil → Guru dan Tenaga Kependidikan** | Foto, nama, jabatan/kelas yang diampu. Tanpa NIP, tanggal lahir, nomor HP | Wajib |
| **Profil → Sarana dan Prasarana** | Ruang kelas, perpustakaan, Teras Baca, UKS, lapangan, dengan foto | Opsional |
| **Profil → Komite Sekolah** | Susunan komite | Opsional |
| **Berita** | Artikel kegiatan, dengan kategori (Kegiatan, Pengumuman, Artikel Guru) | Wajib |
| **Pengumuman** | Bagian dari Berita (kategori Pengumuman) yang bisa disematkan di Beranda dan punya tanggal berakhir | Wajib |
| **Agenda** | Daftar kegiatan dan libur mendatang (judul, tanggal, tempat). Tidak perlu kalender interaktif | Wajib |
| **Galeri** | Album foto per kegiatan, video lewat tautan YouTube | Wajib |
| **Prestasi** | Lomba yang dimenangkan sekolah, guru, atau murid (lihat aturan nama murid di bagian 6) | Opsional, sangat disarankan |
| **Program → Program Unggulan** | Halaman per program. TEBAK AKSI berisi penjelasan singkat dan tombol ke tebakaksi.sdimalbert.sch.id | Wajib |
| **Program → Ekstrakurikuler** | Pramuka, seni, olahraga, dll. | Opsional |
| **SPMB** | Jadwal, kuota (angka), syarat, jalur, alur, tanya jawab, tautan portal SPMB dinas. Bisa disembunyikan di luar masa SPMB | Wajib saat musim SPMB |
| **Unduhan** | PDF: kalender pendidikan, brosur, formulir, tata tertib | Wajib |
| **Kontak** | Alamat, peta (sematan Google Maps), telepon/WA sekolah, surel, jam layanan, media sosial, formulir pesan | Wajib |
| **Layanan Pengaduan** | Sudah ada di TEBAK AKSI untuk program literasi. Untuk sekolah, cukup formulir Kontak dengan pilihan "Saran/Pengaduan" | Opsional |
| **Alumni** | Halaman cerita atau formulir data alumni | Opsional, tahap 2 |
| **Kebijakan Privasi** | Penjelasan foto dan data yang tampil, cara meminta foto diturunkan | Wajib |

Hal yang sengaja **tidak** dibuat: komentar pengunjung (rawan spam dan perlu dijaga), buku tamu, penghitung pengunjung yang ditampilkan, pendaftaran murid langsung di website (lihat bagian 6), login orang tua.

---

## 3. Pengelola konten, peran, dan alur terbit

| Peran | Siapa | Hak |
|---|---|---|
| **Admin website** | 1–2 orang (operator sekolah atau guru TIK) | Semua: identitas sekolah, logo, menu, halaman profil, data guru, pengguna, menerbitkan dan menurunkan konten |
| **Penulis** | Guru yang ditunjuk (misalnya 3–5 orang) | Menulis berita, menambah album galeri dan agenda. Hanya bisa menyimpan **draf** dan mengajukan untuk ditinjau. Bisa mengubah draf miliknya sendiri |
| **Kepala sekolah** (opsional) | Kepala sekolah | Sama dengan penyunting: membaca dan menerbitkan, tanpa mengatur pengguna |

**Alur terbit**

```
Penulis: Draf  ->  Ajukan  ->  Admin/Kepsek: Terbitkan (atau kembalikan dengan catatan)
                                         |
                                         v
                                   Tayang di website
```

- Admin boleh langsung menerbitkan tulisannya sendiri.
- Tulisan bisa dijadwalkan terbit pada tanggal tertentu.
- Sebelum menerbitkan, peninjau wajib mencentang "Foto sudah diperiksa sesuai aturan privasi" (centang dilakukan manusia, sejalan dengan prinsip TEBAK AKSI).
- Konten yang diturunkan tidak dihapus permanen; statusnya kembali menjadi draf atau diarsipkan.

Bila sekolah lebih suka sederhana, cukup dua peran: Admin dan Penulis.

---

## 4. Hubungan dengan TEBAK AKSI

**Usulan: cukup tautan, tanpa pertukaran data.**

- Halaman Program Unggulan → TEBAK AKSI: penjelasan singkat program (ditulis di website resmi) dan tombol "Kunjungi TEBAK AKSI".
- Kaki halaman memuat tautan "TEBAK AKSI (Teras Baca)".
- Dari TEBAK AKSI ditambahkan tautan balik ke website resmi (perubahan kecil di repo TEBAK AKSI, dikerjakan terpisah bila disetujui).

**Alasan tidak menampilkan data TEBAK AKSI**

- Pengelolaan dipisah. Bila website membaca database TEBAK AKSI, keduanya jadi saling bergantung dan berisiko membuka jalur ke data murid.
- TEBAK AKSI sudah punya halaman publik sendiri (statistik agregat, galeri, diseminasi), jadi tidak perlu digandakan.

**Pilihan tambahan (tidak disarankan untuk tahap 1):** TEBAK AKSI menyediakan satu alamat JSON publik berisi angka agregat saja (misalnya jumlah kegiatan dan karya bulan ini), lalu website resmi menampilkannya di kartu program. Tanpa nama murid, tanpa foto, tanpa akses database. Ini bisa ditambah kemudian tanpa membongkar apa pun.

Website resmi **tidak menyimpan dan tidak menampilkan data murid** (nama, NISN, kelas per anak, nilai, catatan). Satu-satunya kemungkinan nama murid muncul adalah di Prestasi, dengan aturan di bagian 6.

---

## 5. Pilihan teknologi

Batasan: Hostinger (hPanel, SSH, MySQL), tanpa Node di server, `exec()` dan `symlink()` dimatikan di PHP, admin sekolah bukan programmer.

| Pilihan | Kelebihan | Kekurangan |
|---|---|---|
| **A. Laravel 13 + Filament 5 + Tailwind 4** (tumpukan yang sama dengan TEBAK AKSI) | Pemilik sudah menguasai dan sudah punya alur deploy di Hostinger yang sama. Desain bisa persis mengikuti pedoman tanpa gaya template. Tanpa plugin pihak ketiga yang rawan. Panel Filament mudah untuk admin non-programmer. Pola yang sudah teruji bisa dipakai ulang (kompres foto, `HapusAman`, cast tanggal, pengaturan identitas sekolah) | Semua fitur dibangun sendiri (editor berita, galeri, agenda). Perawatan bergantung pada pemilik |
| **B. WordPress** (pasang satu klik di hPanel) | Cepat jadi, banyak tutorial, banyak orang bisa merawat | Tema sekolah umumnya penuh gaya template (slider, gradasi, ikon warna-warni) dan sulit dibersihkan. Plugin adalah sumber utama peretasan website sekolah (judi online, pengalihan). Butuh pembaruan rutin yang sering terlupa. Alur tinjau berita butuh plugin tambahan |
| **C. Situs statis** (Astro/Hugo, dibangun di laptop) | Sangat cepat dan hampir tak bisa diretas | Admin sekolah tidak bisa menulis berita tanpa pemilik. Tidak cocok |

**Rekomendasi: A.** Laravel 13 + Filament 5 + Livewire 4 seperlunya + Tailwind 4, repo baru (misalnya `ridhoachmad712/website-sdimalbert`), database MySQL baru, pengguna database baru.

Rincian:

- Halaman publik: Blade biasa (tanpa Livewire kecuali formulir kontak), di-cache agar ringan di hosting bersama.
- Editor berita: RichEditor Filament (tebal, miring, judul kecil, daftar, tautan, gambar). Tanpa HTML bebas.
- Foto: dikompres dan diperkecil saat unggah (Intervention Image, seperti TEBAK AKSI), metadata EXIF (termasuk lokasi GPS) dibuang.
- SEO: judul dan deskripsi per halaman, peta situs `sitemap.xml`, data terstruktur `School` (nama, alamat, telepon), gambar pratinjau saat dibagikan ke WhatsApp.
- Aset CSS/JS dibangun di laptop dan ikut di-commit, sama seperti TEBAK AKSI.
- Desain mengikuti semangat DESIGN.md TEBAK AKSI (krem, tinta gelap, satu aksen, Nunito), tetapi dengan identitas sekolah. Warna aksen bisa disesuaikan dengan warna logo sekolah. Perlu `DESIGN.md` sendiri di repo baru.

---

## 6. Domain, keamanan, cadangan, dan privasi

### Domain

- **Website resmi di domain utama `sdimalbert.sch.id`** (dan `www.` dialihkan ke sana). TEBAK AKSI tetap di `tebakaksi.sdimalbert.sch.id`.
- **Risiko yang harus dicek dulu lewat SSH:** web root TEBAK AKSI berada **di dalam** `public_html` (`public_html/tebakaksi`). Cara deploy di README TEBAK AKSI (mengganti `public_html` dengan symlink ke folder `public`) **tidak boleh** dipakai untuk website resmi, karena akan memutus folder `tebakaksi` dan TEBAK AKSI ikut mati. Selain itu, berkas `.htaccess` di `public_html` bisa ikut berlaku untuk subdomain di bawahnya. Usulan saat membangun:
  1. Periksa dulu isi `public_html` dan apakah `tebakaksi` di dalamnya berupa symlink atau folder.
  2. Pilihan paling aman: pindahkan folder dokumen subdomain TEBAK AKSI ke luar jalur website resmi (bila hPanel mengizinkan), atau isi `public_html` dengan berkas `public` website resmi (index.php diarahkan ke `~/website-sdimalbert`) sambil mempertahankan `tebakaksi`, dan beri pengecualian di `.htaccess`.
  3. Uji TEBAK AKSI tetap berjalan setelah deploy.
- Apa yang sekarang tayang di `sdimalbert.sch.id` perlu dicek (halaman bawaan Hostinger, website lama, atau kosong). Bila ada website lama, konten pentingnya dipindahkan dan alamat lama dialihkan (301) agar tautan di Google tidak putus.
- Domain `.sch.id` perlu diperpanjang tiap tahun; catat siapa pemegang akun dan tanggal jatuh temponya.

### Keamanan

- HTTPS wajib (SSL gratis hPanel), HSTS setelah stabil.
- Panel admin di `/admin`, login dengan pembatasan percobaan; **verifikasi dua langkah** (aplikasi autentikator, tersedia di Filament) untuk peran Admin.
- Tidak ada pendaftaran akun mandiri; akun Penulis dibuat oleh Admin.
- Unggahan dibatasi: gambar (JPG/PNG/WebP, dikodekan ulang) dan PDF. Tidak ada unggahan berkas lain.
- Formulir kontak: pembatasan kiriman, honeypot; tanpa CAPTCHA pihak ketiga bila tidak perlu.
- Header keamanan dasar (CSP, X-Frame-Options, Referrer-Policy).
- Pengguna database khusus website, berbeda dari TEBAK AKSI.
- `APP_DEBUG=false` di produksi, `.env` di luar web root.
- Catatan aktivitas admin sederhana: siapa menerbitkan/menurunkan apa dan kapan.

### Cadangan data

- Cadangan otomatis Hostinger (cek frekuensi paket: harian atau mingguan).
- Ditambah Cron Job hPanel harian yang menjalankan `mysqldump` dan mengarsipkan folder `storage/app/public` (cron hPanel menjalankan perintah shell, jadi tidak terhalang `exec()` yang dimatikan di PHP). Simpan 14 cadangan terakhir.
- Tombol "Unduh cadangan database" di panel admin; sebulan sekali admin menyimpan salinan ke Google Drive sekolah.
- Uji pemulihan sekali setelah peluncuran.

### Privasi foto dan data murid

Data anak termasuk data pribadi yang dilindungi (UU No. 27 Tahun 2022 tentang Pelindungan Data Pribadi mensyaratkan persetujuan orang tua/wali).

- **Persetujuan orang tua tertulis** untuk publikasi foto, dikumpulkan tiap awal tahun ajaran (lembar persetujuan di Unduhan). Murid yang tidak setuju tidak boleh tampak jelas di foto.
- **Tidak memasangkan nama dengan wajah.** Keterangan foto menyebut kegiatan dan kelas, bukan nama murid.
- Utamakan foto kelompok, dari samping atau belakang, atau fokus pada karya. Hindari foto murid sendirian dari dekat, berseragam olahraga/renang, atau yang memperlihatkan papan nama/nametag.
- Foto diperkecil (misalnya lebar maks. 1600 px) dan EXIF dibuang otomatis.
- Prestasi murid: tampilkan **nama depan dan kelas** saja, hanya bila orang tua setuju; selain itu cukup "Murid kelas V".
- Halaman Kebijakan Privasi dan alamat kontak untuk meminta foto diturunkan; admin menurunkan dalam waktu singkat (misalnya 2 hari kerja).
- Data guru yang tampil: foto, nama, jabatan. Tanpa NIP, tanggal lahir, alamat, nomor HP pribadi, kecuali guru bersangkutan setuju.
- Tidak ada pendaftaran murid baru lewat website ini. SPMB SD negeri di Makassar berjalan lewat portal dinas; website cukup memberi informasi dan tautan, sehingga website tidak menyimpan data calon murid.
- Seeder dan tes memakai data contoh, bukan data asli.

---

## 7. Perkiraan tahap

| Tahap | Isi |
|---|---|
| 1 (inti) | Identitas sekolah, Beranda, Profil lengkap, Guru dan Tendik, Berita + Pengumuman dengan alur terbit, Agenda, Galeri, Program (termasuk TEBAK AKSI), SPMB, Unduhan, Kontak + peta, Kebijakan Privasi, SEO dasar, cadangan |
| 2 | Prestasi, Ekstrakurikuler, Sarana Prasarana, Komite, kartu angka agregat dari TEBAK AKSI (bila disetujui), Alumni |

---

## 8. Pertanyaan untuk pemilik sebelum membangun

**Domain dan server**
1. Apa yang sekarang tayang di `sdimalbert.sch.id`? Ada website lama yang kontennya perlu dipindahkan?
2. Boleh saya memeriksa isi `public_html` lewat SSH (hanya baca) saat mulai membangun, untuk memastikan TEBAK AKSI tidak terganggu?
3. Paket Hostinger yang dipakai menyediakan cadangan harian atau mingguan?

**Pengelolaan**
4. Siapa yang akan menjadi Admin website, dan berapa guru yang akan menjadi Penulis?
5. Cukup dua peran (Admin dan Penulis), atau kepala sekolah juga ikut meninjau sebelum terbit?
6. Apakah admin TEBAK AKSI sama orangnya dengan admin website? (Akunnya tetap terpisah, ini hanya untuk menyusun panduan.)

**Isi**
7. Dari daftar halaman di bagian 2, ada yang ingin dibuang atau ditambah? Apakah Prestasi masuk tahap 1?
8. Program unggulan apa saja selain TEBAK AKSI?
9. Apakah sudah ada bahan: sejarah, visi-misi, struktur organisasi, foto guru, logo resolusi tinggi, nomor kontak resmi, akun media sosial?
10. Halaman SPMB: apakah sekolah ikut portal SPMB Dinas Pendidikan Kota Makassar, atau ada pendaftaran langsung di sekolah?

**Privasi**
11. Apakah sekolah sudah punya lembar persetujuan publikasi foto dari orang tua? Bila belum, saya siapkan contohnya untuk diunduh.
12. Nama murid di Prestasi: boleh nama depan + kelas (dengan persetujuan orang tua), atau tanpa nama sama sekali?
13. Data guru yang boleh tampil: cukup foto, nama, jabatan? NIP ditampilkan atau tidak?

**Teknologi dan tampilan**
14. Setuju dengan rekomendasi Laravel + Filament (pilihan A), bukan WordPress?
15. Hubungan dengan TEBAK AKSI cukup tautan dua arah di tahap 1?
16. Warna aksen: ikut jingga TEBAK AKSI atau menyesuaikan warna logo sekolah?
17. Nama repo baru dan folder aplikasi di server, misalnya `website-sdimalbert` dan `~/website-sdimalbert`?

---

## 9. Keputusan pemilik (18 Oktober 2026)

| No | Pertanyaan | Keputusan | Akibat pada rancangan |
|---|---|---|---|
| 1 | Website lama di `sdimalbert.sch.id` | Dibuat baru, konten lama tidak dipindahkan | Website baru menggantikan yang lama di domain utama. Berkas lama diarsipkan di server (tidak dihapus) sebelum deploy |
| 2 | Pengelola | Admin adalah guru yang ditunjuk khusus. Kepala sekolah tidak punya akses dan tidak meninjau | Peran Kepala Sekolah dihapus. Admin menerbitkan langsung |
| 3 | Teknologi dan hubungan dengan TEBAK AKSI | Setuju: Laravel + Filament, cukup tautan | Sesuai bagian 4 dan 5 |
| 4 | Persetujuan foto dari orang tua | Belum ada, akan diurus manual oleh sekolah | Website tidak menyimpan data persetujuan. Centang "Foto sudah diperiksa" sebelum terbit tetap ada |
| 5 | Nama murid di Prestasi | Boleh nama murid, dikurasi dan diverifikasi manual oleh admin | Kolom nama murid di Prestasi bebas diisi admin. Aturan "nama depan + kelas" di bagian 6 tidak berlaku |
| 6 | SPMB | Mengikuti portal Dinas Pendidikan Kota Makassar | Halaman SPMB berisi informasi dan tautan ke portal; tanpa formulir pendaftaran |
| 7 | Warna aksen | Diatur admin | Warna aksen menjadi pengaturan di panel admin (satu warna, dengan pengecekan kontras agar teks tombol tetap terbaca) |

## 10. Keputusan lanjutan dan hasil cek server (18 Oktober 2026)

- Semua konten dikelola Admin (tanpa peran Penulis). Berita punya isian **Nama penulis** yang bebas diisi admin bila tulisan berasal dari guru lain.
- Program unggulan: hanya TEBAK AKSI, jadi menu "Program" langsung menuju halaman TEBAK AKSI.
- Data guru: foto, nama, jabatan, NIP, motto, profil, media sosial, dan tombol tautan luar, semuanya diatur admin.
- Repo `website-sdimalbert`, folder server `~/website-sdimalbert`. Pembangunan tahap 1 disetujui.
- Cek server: website lama adalah aplikasi Laravel di `~/domains/sdimalbert.sch.id/website`. `public_html` berupa folder biasa berisi berkas `public` + `index.php` yang menunjuk ke `../website`, dengan `storage` dan `tebakaksi` sebagai symlink (`tebakaksi -> ~/tebak-aksi/public`). Susunan ini sudah terbukti berjalan, jadi deploy baru memakai pola yang sama: ganti isi `public_html` kecuali `tebakaksi`, arahkan `index.php` ke `~/website-sdimalbert`. Server PHP 8.4, MariaDB 11.8, `mysqldump` tersedia.

## 12. Tambahan setelah tahap 1 (19 Oktober 2026)

- Beranda: hero foto penuh, berita utama, sambutan berfoto, carousel guru, pita TEBAK AKSI, mosaik galeri, ajakan penutup. Urutan dan judul bagian, tombol, menu, dan tautan kaki halaman diatur admin.
- Warna latar putih dan abu lembut (tanpa krem).
- **Transparansi anggaran**: RKAS dan laporan realisasi penggunaan dana BOSP per tahun dan per tahap, dengan ringkasan angka opsional, sesuai kewajiban publikasi dalam petunjuk teknis BOSP.
- Foto kecil WebP otomatis, pencarian, pratinjau draf berita, data terstruktur NewsArticle.
- Ekstrakurikuler, sarana dan prasarana bergaleri, dan alumni (cerita oleh admin dan formulir kiriman yang diperiksa admin).
