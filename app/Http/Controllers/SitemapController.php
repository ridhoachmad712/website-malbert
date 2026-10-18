<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Berita;
use App\Models\Guru;
use App\Models\Halaman;
use App\Models\Pengaturan;
use Illuminate\Http\Response;

/** Peta situs untuk mesin pencari: semua halaman publik yang sudah terbit. */
class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $alamat = collect(['beranda', 'profil', 'guru', 'berita', 'agenda', 'galeri', 'prestasi', 'unduhan', 'tebakaksi', 'kontak'])
            ->map(fn ($rute) => ['loc' => route($rute), 'lastmod' => null]);

        if (Pengaturan::aktif('spmb_tampil')) {
            $alamat->push(['loc' => route('spmb'), 'lastmod' => null]);
        }

        $alamat = $alamat
            ->merge(Halaman::terbit()->get()->map(fn ($item) => ['loc' => $item->url(), 'lastmod' => $item->updated_at]))
            ->merge(Berita::terbit()->get()->map(fn ($item) => ['loc' => $item->url(), 'lastmod' => $item->updated_at]))
            ->merge(Album::terbit()->get()->map(fn ($item) => ['loc' => $item->url(), 'lastmod' => $item->updated_at]))
            ->merge(Guru::where('tampil', true)->get()->map(fn ($item) => ['loc' => $item->url(), 'lastmod' => $item->updated_at]));

        return response()->view('sitemap', ['alamat' => $alamat], 200, ['Content-Type' => 'application/xml']);
    }
}
