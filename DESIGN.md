# Pedoman Desain Website SDI Malbert

Acuan tetap untuk setiap perubahan tampilan. Bersaudara dengan pedoman TEBAK AKSI (kertas krem, tinta gelap, satu warna aksen), tetapi dengan identitas sekolah. Setiap keputusan visual punya alasan; hiasan tanpa fungsi tidak dipakai.

## Arah

> Papan informasi sekolah yang rapi dan ramah: mudah dibaca orang tua di HP, terasa resmi bagi dinas, tetap hangat untuk sekolah dasar.

| Bagian | ENERGY | RHYTHM | MOTION | Alasan |
|---|---|---|---|---|
| Halaman publik | 2 (seimbang) | 2 (ada variasi) | 1 (hanya hover/fokus) | Memperkenalkan sekolah, tetapi pembaca datang untuk mencari informasi. |
| Panel admin | 1 (tenang) | 1 (seragam) | 1 | Dipakai guru admin di sela mengajar. |

Setiap layar punya **satu titik fokus**. Beranda: hero berfoto, lalu kabar sekolah terbaru (atau pengumuman yang disematkan). SPMB: status dan tombol portal. Kontak: formulir pesan.

## Warna

| Peran | Token | Dipakai untuk |
|---|---|---|
| Dasar | `krem` `#fffaf3` | Latar halaman |
| Permukaan | `white` | Panel dan kartu |
| Garis | `garis` `#ece3d6` | Batas panel, pemisah baris |
| Tinta | `slate-800`/`slate-900` | Teks utama dan judul |
| Teks pendukung | `slate-600` | Keterangan. **Jangan memakai `slate-400` untuk teks.** |
| Aksen | `brand-700` | Tombol utama, menu aktif, tautan, nomor langkah. Hanya untuk tindakan dan penanda, bukan hiasan. |
| Gelap | `slate-900`/`slate-950` | Latar hero tanpa foto, lapisan foto hero, dan pita Program TEBAK AKSI. Tombol kedua di atasnya memakai `tombol-terang`. |

Admin memilih aksen dari palet yang sudah diuji kontras (`App\Support\PaletWarna`: Jingga, Toska, Biru, Hijau, Merah Bata, Nila), bukan kode warna bebas. Tidak ada gradasi, bulatan blur, efek kaca, atau ubin ikon warna-warni.

## Beranda

- **Hero**: satu foto selebar layar (tanpa slider) dengan **lapisan gelap rata** `slate-950/60` agar tulisan putih terbaca. Lapisan ini bukan gradasi. Tanpa foto, hero berlatar `slate-900` polos dengan logo sekolah.
- **Ritme latar**: bagian bergantian krem dan putih selebar layar (dibatasi garis atas-bawah). Program TEBAK AKSI satu-satunya **pita gelap** `slate-900`.
- **Kepala bagian** seragam (`x-kepala-bagian`): label kecil berwarna aksen, judul, dan tautan "Lihat semua". Label dan judul diatur admin.
- **Carousel guru**: digeser manual (jari, tombol, papan ketik), **tanpa putar otomatis**, sesuai WCAG 2.2.2.
- **Foto pengganti** untuk berita atau prestasi tanpa gambar: logo sekolah di atas `brand-50`, bukan foto stok.
- Urutan dan tampil-tidaknya bagian diatur admin; bagian yang isinya belum ada tidak ditampilkan.

## Bentuk

- Sudut: `rounded-lg` untuk tombol, isian, foto kecil; `rounded-xl` untuk panel dan foto besar.
- Bayangan: panel **tanpa bayangan**, cukup garis. Bayangan hanya untuk yang melayang: menu tarik-turun, menu HP, dan kartu angka yang menumpuk di tepi bawah hero.
- Fokus keyboard: garis 2px `brand-700`.
- Target sentuh minimal 44px.

## Huruf

- **Nunito**, disimpan di server sendiri (tanpa layanan huruf pihak ketiga).
- Judul memakai huruf biasa dan tebal. Tidak ada label kapital berspasi lebar.

## Ikon, foto, dan emoji

- **Tidak ada emoji.**
- Heroicons (garis) hanya untuk menu, tombol tindakan, dan penampil foto. Panah hanya di tombol utama yang membawa ke halaman berikutnya; ikon "buka di tab baru" untuk tautan keluar.
- Foto guru tanpa unggahan diganti inisial di atas `brand-50`, bukan siluet.
- Foto murid: lihat aturan privasi di `KEBUTUHAN-WEBSITE.md`. Keterangan foto tidak menyebut nama murid.

## Teks

- Bahasa sehari-hari, kalimat pendek, kalimat aktif. Sapaan seperlunya.
- **Keadaan kosong** selalu menyebut sebabnya dan langkah berikutnya bagi pengunjung. Contoh: "Belum ada agenda yang akan datang. Jadwal kegiatan dan hari libur akan tampil di sini setelah diumumkan sekolah."
- **Angka** hanya yang diisi admin atau dihitung dari data; bagian angka disembunyikan bila datanya belum ada.
