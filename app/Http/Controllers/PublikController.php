<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Album;
use App\Models\Berita;
use App\Models\Guru;
use App\Models\Halaman;
use App\Models\Pengaturan;
use App\Models\Pesan;
use App\Models\Prestasi;
use App\Models\Unduhan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PublikController extends Controller
{
    public function beranda(): View
    {
        return view('publik.beranda', [
            'disematkan' => Berita::disematkan()->latest('terbit_pada')->limit(3)->get(),
            'berita' => Berita::terbit()->latest('terbit_pada')->limit(3)->get(),
            'agenda' => Pengaturan::aktif('beranda_tampil_agenda') ? Agenda::mendatang()->limit(4)->get() : collect(),
            'prestasi' => Pengaturan::aktif('beranda_tampil_prestasi') ? Prestasi::terbit()->latest('tanggal')->limit(3)->get() : collect(),
            'album' => Pengaturan::aktif('beranda_tampil_galeri')
                ? Album::terbit()->has('foto')->with('sampul')->withCount('foto')->latest('tanggal')->limit(4)->get()
                : collect(),
            'jumlahGuru' => Guru::where('tampil', true)->count(),
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
                ->with('sampul')->withCount('foto')->latest('tanggal')->paginate(12),
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
