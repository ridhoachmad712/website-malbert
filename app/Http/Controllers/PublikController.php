<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Album;
use App\Models\Alumni;
use App\Models\Berita;
use App\Models\DokumenAnggaran;
use App\Models\Ekstrakurikuler;
use App\Models\Guru;
use App\Models\Halaman;
use App\Models\Pengaturan;
use App\Models\Pesan;
use App\Models\Prestasi;
use App\Models\Sarana;
use App\Models\Unduhan;
use App\Support\BagianBeranda;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PublikController extends Controller
{
    public function beranda(): View
    {
        $bagian = BagianBeranda::tampil();
        $ambil = fn (string $kunci, Closure $query) => isset($bagian[$kunci]) ? $query() : collect();

        $data = [
            'disematkan' => Berita::disematkan()->latest('terbit_pada')->limit(3)->get(),
            'berita' => $ambil('berita', fn () => Berita::terbit()->latest('terbit_pada')->limit(4)->get()),
            'guru' => $ambil('guru', fn () => Guru::tampil()->get()),
            'agenda' => $ambil('agenda', fn () => Agenda::mendatang()->limit(4)->get()),
            'prestasi' => $ambil('prestasi', fn () => Prestasi::terbit()->latest('tanggal')->limit(3)->get()),
            'album' => $ambil('galeri', fn () => Album::terbit()->has('foto')->denganSampul()->withCount('foto')->latest('tanggal')->limit(5)->get()),
            'jumlahGuru' => Guru::where('tampil', true)->count(),
        ];

        // Bagian yang isinya belum ada dilewati, agar Beranda tidak berisi kotak kosong.
        // Berita tetap tampil (dengan keterangan kosong) karena menjadi titik fokus Beranda.
        $ada = [
            'berita' => true,
            'sambutan' => filled(Pengaturan::ambil('kepsek_nama')) && filled(Pengaturan::ambil('kepsek_sambutan_singkat')),
            'guru' => $data['guru']->isNotEmpty(),
            'agenda' => $data['agenda']->isNotEmpty(),
            'program' => true,
            'prestasi' => $data['prestasi']->isNotEmpty(),
            'galeri' => $data['album']->isNotEmpty(),
            'ajakan' => filled(Pengaturan::ambil('ajakan_judul')),
        ];

        return view('publik.beranda', $data + [
            'bagian' => array_filter($bagian, fn ($item) => $ada[$item['kunci']] ?? false),
        ]);
    }

    public function profil(): View
    {
        return view('publik.profil', [
            'halaman' => $this->halamanProfil(),
            'jumlahGuru' => Guru::where('tampil', true)->count(),
        ]);
    }

    public function halaman(Halaman $halaman): View
    {
        abort_unless($halaman->terbit, 404);

        return view('publik.halaman', [
            'halaman' => $halaman,
            'lainnya' => $halaman->grup === 'profil' ? $this->halamanProfil() : collect(),
        ]);
    }

    public function guru(): View
    {
        $guru = Guru::tampil()->get()->groupBy('kelompok');

        return view('publik.guru', [
            'kelompok' => collect(Guru::KELOMPOK)->map(fn ($label, $kunci) => ['label' => $label, 'guru' => $guru->get($kunci, collect())])
                ->filter(fn ($item) => $item['guru']->isNotEmpty()),
        ]);
    }

    public function guruDetail(Guru $guru): View
    {
        abort_unless($guru->tampil, 404);

        return view('publik.guru-detail', ['guru' => $guru]);
    }

    public function berita(Request $request): View
    {
        $kategori = array_key_exists($request->query('kategori'), Berita::KATEGORI) ? $request->query('kategori') : null;

        return view('publik.berita', [
            'kategori' => $kategori,
            'berita' => Berita::terbit()->when($kategori, fn ($query) => $query->where('kategori', $kategori))
                ->latest('terbit_pada')->paginate(9)->withQueryString(),
        ]);
    }

    public function beritaDetail(Berita $berita): View
    {
        abort_unless(Berita::terbit()->whereKey($berita->id)->exists(), 404);

        return view('publik.berita-detail', [
            'berita' => $berita,
            'lainnya' => Berita::terbit()->whereKeyNot($berita->id)->latest('terbit_pada')->limit(3)->get(),
        ]);
    }

    public function agenda(): View
    {
        return view('publik.agenda', [
            'mendatang' => Agenda::mendatang()->get()->groupBy(fn (Agenda $agenda) => $agenda->mulai->translatedFormat('F Y')),
            'lewat' => Agenda::lewat()->limit(10)->get(),
        ]);
    }

    public function galeri(): View
    {
        return view('publik.galeri', [
            'album' => Album::terbit()->where(fn ($query) => $query->has('foto')->orWhereNotNull('video_youtube'))
                ->denganSampul()->withCount('foto')->latest('tanggal')->paginate(12),
        ]);
    }

    public function galeriDetail(Album $album): View
    {
        abort_unless($album->terbit, 404);

        return view('publik.galeri-detail', ['album' => $album->load('foto')]);
    }

    public function prestasi(): View
    {
        return view('publik.prestasi', [
            'prestasi' => Prestasi::terbit()->latest('tanggal')->paginate(12),
        ]);
    }

    public function unduhan(): View
    {
        return view('publik.unduhan', [
            'kelompok' => Unduhan::terbit()->orderBy('urutan')->orderBy('judul')->get()
                ->groupBy(fn (Unduhan $unduhan) => $unduhan->kategori ?: 'Lainnya'),
        ]);
    }

    public function ekskul(): View
    {
        return view('publik.ekskul', ['ekskul' => Ekstrakurikuler::tampil()->get()]);
    }

    public function ekskulDetail(Ekstrakurikuler $ekstrakurikuler): View
    {
        abort_unless($ekstrakurikuler->tampil, 404);

        return view('publik.ekskul-detail', [
            'ekskul' => $ekstrakurikuler->load(['album' => fn ($query) => $query->where('terbit', true)->with('foto')]),
            'lainnya' => Ekstrakurikuler::tampil()->whereKeyNot($ekstrakurikuler->id)->get(),
        ]);
    }

    public function sarana(): View
    {
        return view('publik.sarana', ['sarana' => Sarana::tampil()->get()]);
    }

    public function transparansi(Request $request): View
    {
        $daftarTahun = DokumenAnggaran::terbit()->distinct()->orderByDesc('tahun')->pluck('tahun');
        $tahun = $daftarTahun->contains((int) $request->query('tahun')) ? (int) $request->query('tahun') : $daftarTahun->first();

        $dokumen = $tahun
            ? DokumenAnggaran::terbit()->where('tahun', $tahun)->orderByRaw("CASE jenis WHEN 'rkas' THEN 0 WHEN 'realisasi_tahap1' THEN 1 WHEN 'realisasi_tahap2' THEN 2 WHEN 'realisasi_tahunan' THEN 3 WHEN 'pertanggungjawaban' THEN 4 ELSE 5 END")->orderBy('tanggal')->get()
            : collect();

        return view('publik.transparansi', [
            'daftarTahun' => $daftarTahun,
            'tahun' => $tahun,
            'dokumen' => $dokumen,
        ]);
    }

    public function alumni(): View
    {
        return view('publik.alumni', ['alumni' => Alumni::tampil()->get()]);
    }

    public function kirimAlumni(Request $request): RedirectResponse
    {
        abort_unless(Pengaturan::aktif('alumni_formulir_tampil'), 404);

        if (filled($request->input('situs'))) {
            return redirect()->route('alumni')->with('terkirim', true);
        }

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'tahun_lulus' => ['required', 'integer', 'min:1950', 'max:'.now()->year],
            'kegiatan' => ['nullable', 'string', 'max:200'],
            'cerita' => ['nullable', 'string', 'max:1500'],
            'kontak' => ['nullable', 'string', 'max:120'],
            'setuju_tampil' => ['nullable', 'boolean'],
        ], [], ['tahun_lulus' => 'tahun lulus', 'cerita' => 'cerita']);

        Alumni::create($data + ['sumber' => 'formulir', 'status' => 'menunggu', 'setuju_tampil' => (bool) ($data['setuju_tampil'] ?? false)]);

        return redirect()->route('alumni')->with('terkirim', true);
    }

    public function cari(Request $request): View
    {
        $kata = trim(mb_substr((string) $request->query('q'), 0, 100));
        $hasil = collect();

        if (mb_strlen($kata) >= 3) {
            $pola = '%'.addcslashes($kata, '%_\\').'%';
            $cocok = fn ($query, array $kolom) => $query->where(fn ($query) => collect($kolom)->each(fn ($k) => $query->orWhere($k, 'like', $pola)));

            $hasil = collect()
                ->merge($cocok(Berita::terbit(), ['judul', 'ringkasan', 'isi'])->latest('terbit_pada')->limit(20)->get()
                    ->map(fn ($item) => ['jenis' => $item->namaKategori(), 'judul' => $item->judul, 'url' => $item->url(), 'cuplikan' => $item->cuplikan(), 'tanggal' => $item->terbit_pada]))
                ->merge($cocok(Halaman::terbit(), ['judul', 'ringkasan', 'isi'])->limit(10)->get()
                    ->map(fn ($item) => ['jenis' => 'Halaman', 'judul' => $item->judul, 'url' => $item->url(), 'cuplikan' => $item->ringkasan, 'tanggal' => null]))
                ->merge($cocok(Agenda::query(), ['judul', 'keterangan', 'tempat'])->orderByDesc('mulai')->limit(10)->get()
                    ->map(fn ($item) => ['jenis' => 'Agenda', 'judul' => $item->judul, 'url' => route('agenda'), 'cuplikan' => $item->tanggalTeks(), 'tanggal' => null]))
                ->merge($cocok(Ekstrakurikuler::where('tampil', true), ['nama', 'ringkasan', 'deskripsi'])->limit(10)->get()
                    ->map(fn ($item) => ['jenis' => 'Ekstrakurikuler', 'judul' => $item->nama, 'url' => $item->url(), 'cuplikan' => $item->ringkasan, 'tanggal' => null]))
                ->merge($cocok(Guru::where('tampil', true), ['nama', 'jabatan'])->limit(10)->get()
                    ->map(fn ($item) => ['jenis' => 'Guru dan tendik', 'judul' => $item->nama, 'url' => $item->url(), 'cuplikan' => $item->jabatan, 'tanggal' => null]))
                ->merge($cocok(Unduhan::terbit(), ['judul', 'keterangan', 'kategori'])->limit(10)->get()
                    ->map(fn ($item) => ['jenis' => 'Unduhan', 'judul' => $item->judul, 'url' => $item->url(), 'cuplikan' => $item->keterangan, 'tanggal' => null]))
                ->merge($cocok(DokumenAnggaran::terbit(), ['judul', 'keterangan', 'periode'])->limit(10)->get()
                    ->map(fn ($item) => ['jenis' => 'Transparansi anggaran', 'judul' => $item->judul, 'url' => route('transparansi', ['tahun' => $item->tahun]), 'cuplikan' => $item->namaJenis(), 'tanggal' => null]));
        }

        return view('publik.cari', ['kata' => $kata, 'hasil' => $hasil]);
    }

    public function pratinjauBerita(Berita $berita): View
    {
        return view('publik.berita-detail', [
            'berita' => $berita,
            'lainnya' => collect(),
            'pratinjau' => true,
        ]);
    }

    public function tebakAksi(): View
    {
        return view('publik.tebak-aksi');
    }

    public function spmb(): View
    {
        return view('publik.spmb');
    }

    public function kontak(): View
    {
        return view('publik.kontak');
    }

    public function kirimPesan(Request $request): RedirectResponse
    {
        // Isian "situs" tersembunyi dari manusia; bila terisi, pengirimnya hampir pasti program spam
        if (filled($request->input('situs'))) {
            return back()->with('terkirim', true);
        }

        $data = $request->validate([
            'nama' => ['nullable', 'string', 'max:120'],
            'kontak' => ['nullable', 'string', 'max:120'],
            'kategori' => ['required', Rule::in(array_keys(Pesan::KATEGORI))],
            'isi' => ['required', 'string', 'min:10', 'max:3000'],
        ], [], [
            'isi' => 'pesan',
        ]);

        Pesan::create($data + ['status' => 'baru']);

        return redirect()->route('kontak')->with('terkirim', true);
    }

    protected function halamanProfil()
    {
        return Halaman::terbit()->where('grup', 'profil')->orderBy('urutan')->orderBy('judul')->get(['id', 'judul', 'slug', 'ringkasan']);
    }
}
