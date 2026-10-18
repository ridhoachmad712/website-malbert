<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * Isian tunggal yang diatur admin: identitas sekolah, kepala sekolah, beranda, program TEBAK AKSI, SPMB, dan tampilan.
 * Nilai bawaan ada di BAWAAN; yang tersimpan di database menimpanya.
 */
class Pengaturan extends Model
{
    public $timestamps = false;

    public $incrementing = false;

    protected $table = 'pengaturan';

    protected $primaryKey = 'kunci';

    protected $keyType = 'string';

    protected $fillable = ['kunci', 'nilai'];

    public const BAWAAN = [
        // Identitas sekolah
        'nama_sekolah' => 'UPT SPF SD Inpres Mallengkeri Bertingkat',
        'nama_singkat' => 'SDI Malbert',
        'npsn' => '',
        'status_sekolah' => 'Negeri',
        'akreditasi' => '',
        'kurikulum' => 'Kurikulum Merdeka',
        'tahun_berdiri' => '',
        'alamat' => '',
        'kelurahan' => '',
        'kecamatan' => '',
        'kota' => 'Kota Makassar',
        'provinsi' => 'Sulawesi Selatan',
        'kode_pos' => '',
        'telepon' => '',
        'email' => '',
        'whatsapp' => '',
        'jam_layanan' => 'Senin–Jumat, 07.30–14.00 WITA',
        'tautan_peta' => '',
        'peta_sematan' => '',
        'tautan_data_resmi' => '',
        'jumlah_rombel' => '',
        'jumlah_murid' => '',
        'logo_sekolah' => '',
        'medsos_instagram' => '',
        'medsos_facebook' => '',
        'medsos_youtube' => '',
        'medsos_tiktok' => '',
        'tema_warna' => 'jingga',

        // Kepala sekolah
        'kepsek_nama' => '',
        'kepsek_nip' => '',
        'kepsek_foto' => '',
        'kepsek_sambutan_singkat' => '',
        'kepsek_sambutan' => '',

        // Beranda
        'beranda_judul' => 'Belajar dengan gembira, tumbuh dengan karakter.',
        'beranda_paragraf' => 'Sekolah dasar negeri di Kota Makassar yang membiasakan murid membaca setiap hari dan bertumbuh menjadi pribadi yang santun dan percaya diri.',
        'beranda_foto' => '',
        'beranda_keterangan_foto' => '',
        'beranda_tampil_sambutan' => true,
        'beranda_tampil_agenda' => true,
        'beranda_tampil_program' => true,
        'beranda_tampil_prestasi' => true,
        'beranda_tampil_galeri' => true,

        // Program unggulan TEBAK AKSI
        'tebakaksi_kepanjangan' => 'Teras Baca Kelas Asah Karakter dan Literasi',
        'tebakaksi_ringkasan' => 'Program literasi harian: murid membaca 15 menit di teras kelas, merangkum, lalu menceritakan kembali bacaannya, sementara guru membina karakter mereka.',
        'tebakaksi_isi' => '',
        'tebakaksi_poin' => [
            ['judul' => 'Membaca 15 menit', 'keterangan' => 'Setiap hari sekolah, murid membaca buku pilihannya di teras kelas.'],
            ['judul' => 'Merangkum dan bercerita', 'keterangan' => 'Murid menuliskan isi bacaan dengan bahasanya sendiri, lalu menceritakannya kembali.'],
            ['judul' => 'Membina karakter', 'keterangan' => 'Guru mendampingi dan memantau perubahan sikap murid secara berkala.'],
        ],
        'tebakaksi_url' => 'https://tebakaksi.sdimalbert.sch.id',
        'tebakaksi_gambar' => '',

        // Penerimaan murid baru (SPMB)
        'spmb_tampil' => false,
        'spmb_status' => 'segera',
        'spmb_tahun_ajaran' => '',
        'spmb_ringkasan' => 'Pendaftaran murid baru SD negeri di Kota Makassar dilakukan secara daring melalui portal SPMB Dinas Pendidikan Kota Makassar.',
        'spmb_portal_url' => '',
        'spmb_jadwal' => [],
        'spmb_jalur' => [],
        'spmb_syarat' => '',
        'spmb_alur' => [],
        'spmb_tanya' => [],
        'spmb_narahubung' => '',

        // Lain-lain
        'cadangan_terakhir' => '',
    ];

    public const STATUS_SPMB = [
        'segera' => 'Segera dibuka',
        'dibuka' => 'Pendaftaran dibuka',
        'ditutup' => 'Pendaftaran ditutup',
    ];

    public static function semua(): array
    {
        return Cache::rememberForever('pengaturan', fn () => array_merge(
            self::BAWAAN,
            static::query()->pluck('nilai', 'kunci')->all(),
        ));
    }

    public static function ambil(string $kunci, mixed $bawaan = null): mixed
    {
        return self::semua()[$kunci] ?? $bawaan;
    }

    public static function aktif(string $kunci): bool
    {
        return filter_var(self::ambil($kunci, false), FILTER_VALIDATE_BOOLEAN);
    }

    /** Pengaturan berbentuk daftar (disimpan sebagai JSON). */
    public static function daftar(string $kunci): array
    {
        $nilai = self::ambil($kunci, []);

        return is_array($nilai) ? $nilai : (json_decode((string) $nilai, true) ?: []);
    }

    /** Alamat publik berkas yang disimpan di pengaturan (logo, foto kepala sekolah, dst.). */
    public static function urlBerkas(string $kunci): ?string
    {
        $path = self::ambil($kunci);

        return filled($path) ? Storage::disk('public')->url($path) : null;
    }

    /** Alamat lengkap dalam satu baris, dari bagian yang sudah diisi. */
    public static function alamatLengkap(): string
    {
        $bagian = [self::ambil('alamat')];
        foreach (['kelurahan' => 'Kel. ', 'kecamatan' => 'Kec. '] as $kunci => $awalan) {
            if (filled($nilai = self::ambil($kunci))) {
                $bagian[] = $awalan.$nilai;
            }
        }
        $bagian[] = trim(self::ambil('kota').' '.self::ambil('kode_pos'));
        $bagian[] = self::ambil('provinsi');

        return implode(', ', array_filter($bagian, 'filled'));
    }

    /** Nomor WhatsApp sekolah dalam bentuk tautan wa.me, atau null bila belum diisi. */
    public static function tautanWhatsapp(): ?string
    {
        $nomor = preg_replace('/\D/', '', (string) self::ambil('whatsapp'));
        if ($nomor === '') {
            return null;
        }

        return 'https://wa.me/'.(str_starts_with($nomor, '0') ? '62'.substr($nomor, 1) : $nomor);
    }

    /** @return array<string, string> nama layanan => alamat */
    public static function mediaSosial(): array
    {
        return array_filter([
            'Instagram' => self::ambil('medsos_instagram'),
            'Facebook' => self::ambil('medsos_facebook'),
            'YouTube' => self::ambil('medsos_youtube'),
            'TikTok' => self::ambil('medsos_tiktok'),
        ]);
    }

    public static function simpan(array $data): void
    {
        foreach ($data as $kunci => $nilai) {
            $nilai = match (true) {
                is_array($nilai) => json_encode(array_values($nilai)),
                is_bool($nilai) => $nilai ? '1' : '0',
                default => $nilai ?? '',
            };
            static::updateOrCreate(['kunci' => $kunci], ['nilai' => $nilai]);
        }

        Cache::forget('pengaturan');
    }
}
