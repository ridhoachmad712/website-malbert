@props(['judul' => null, 'deskripsi' => null, 'gambar' => null])

@use('App\Models\Halaman')
@use('App\Models\Pengaturan')

@php
    $namaSekolah = Pengaturan::ambil('nama_sekolah');
    $deskripsi ??= Pengaturan::ambil('beranda_paragraf');
    $gambar ??= Pengaturan::urlBerkas('beranda_foto') ?? Pengaturan::urlBerkas('logo_sekolah');

    $halamanProfil = Halaman::terbit()->where('grup', 'profil')->orderBy('urutan')->orderBy('judul')->get(['judul', 'slug']);
    $menuProfil = collect([['url' => route('profil'), 'label' => 'Identitas dan sambutan']])
        ->merge($halamanProfil->map(fn ($item) => ['url' => route('halaman', $item->slug), 'label' => $item->judul]))
        ->push(['url' => route('guru'), 'label' => 'Guru dan tenaga kependidikan']);
    $menuInformasi = collect([
        ['url' => route('berita'), 'label' => 'Berita'],
        ['url' => route('berita', ['kategori' => 'pengumuman']), 'label' => 'Pengumuman'],
        ['url' => route('agenda'), 'label' => 'Agenda'],
        ['url' => route('galeri'), 'label' => 'Galeri'],
        ['url' => route('prestasi'), 'label' => 'Prestasi'],
        ['url' => route('unduhan'), 'label' => 'Unduhan'],
    ]);

    $sekarang = request()->fullUrl();
    $aktif = fn (string $url) => $sekarang === $url;
    $grupAktif = fn ($menu) => $menu->contains(fn ($item) => request()->url() === strtok($item['url'], '?') || str_starts_with(request()->url().'/', strtok($item['url'], '?').'/'));

    $menuUtama = collect([
        ['label' => 'Beranda', 'url' => route('beranda')],
        ['label' => 'Profil', 'anak' => $menuProfil],
        ['label' => 'Informasi', 'anak' => $menuInformasi],
        ['label' => 'TEBAK AKSI', 'url' => route('tebakaksi')],
        Pengaturan::aktif('spmb_tampil') ? ['label' => 'SPMB', 'url' => route('spmb')] : null,
        ['label' => 'Kontak', 'url' => route('kontak')],
    ])->filter();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#fffaf3">
    <title>{{ $judul ? $judul.' · '.Pengaturan::ambil('nama_singkat') : $namaSekolah }}</title>
    <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags((string) $deskripsi), 160) }}">
    <link rel="canonical" href="{{ request()->url() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $namaSekolah }}">
    <meta property="og:title" content="{{ $judul ?? $namaSekolah }}">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags((string) $deskripsi), 200) }}">
    <meta property="og:url" content="{{ request()->url() }}">
    @if ($gambar)
        <meta property="og:image" content="{{ $gambar }}">
    @endif
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-ikon-situs />
    {{ $kepala ?? '' }}
</head>
<body class="flex min-h-screen flex-col">
    <a href="#isi" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:font-bold">Lompat ke isi</a>

    <header class="sticky top-0 z-30 border-b border-garis bg-white">
        <div class="wadah flex h-16 items-center justify-between gap-4">
            <a href="{{ route('beranda') }}" class="min-w-0 shrink"><x-logo /></a>

            <nav class="hidden h-full items-stretch gap-1 lg:flex" aria-label="Menu utama">
                @foreach ($menuUtama as $item)
                    @if (isset($item['anak']))
                        @php $sedang = $grupAktif($item['anak']); @endphp
                        <details data-menu class="relative flex">
                            <summary @class(['flex cursor-pointer items-center gap-1 border-b-2 px-3 text-sm font-bold',
                                'border-brand-700 text-brand-800' => $sedang,
                                'border-transparent text-slate-600 hover:text-slate-900' => ! $sedang])>
                                {{ $item['label'] }}
                                <x-heroicon-m-chevron-down class="h-4 w-4" aria-hidden="true" />
                            </summary>
                            <div class="menu-turun">
                                @foreach ($item['anak'] as $anak)
                                    <a href="{{ $anak['url'] }}" @if ($aktif($anak['url'])) aria-current="page" @endif>{{ $anak['label'] }}</a>
                                @endforeach
                            </div>
                        </details>
                    @else
                        @php $sedang = $item['url'] === route('beranda') ? request()->routeIs('beranda') : str_starts_with(request()->url(), $item['url']); @endphp
                        <a href="{{ $item['url'] }}" @if ($sedang) aria-current="page" @endif
                           @class(['flex items-center whitespace-nowrap border-b-2 px-3 text-sm font-bold',
                               'border-brand-700 text-brand-800' => $sedang,
                               'border-transparent text-slate-600 hover:text-slate-900' => ! $sedang])>{{ $item['label'] }}</a>
                    @endif
                @endforeach
            </nav>

            <details data-menu class="group lg:hidden">
                <summary class="flex min-h-11 cursor-pointer items-center gap-2 rounded-lg px-3 text-sm font-bold text-slate-700 hover:bg-slate-50">
                    <x-heroicon-o-bars-3 class="h-6 w-6 group-open:hidden" aria-hidden="true" />
                    <x-heroicon-o-x-mark class="hidden h-6 w-6 group-open:block" aria-hidden="true" />
                    <span class="group-open:hidden">Menu</span>
                    <span class="hidden group-open:inline">Tutup</span>
                </summary>
                <nav class="absolute inset-x-0 top-16 max-h-[calc(100dvh-4rem)] overflow-y-auto border-b border-garis bg-white px-4 pb-4 pt-2 shadow-lg" aria-label="Menu utama">
                    @foreach ($menuUtama as $item)
                        @if (isset($item['anak']))
                            <p class="mt-3 px-3 pb-1 text-xs font-bold text-slate-500">{{ $item['label'] }}</p>
                            @foreach ($item['anak'] as $anak)
                                <a href="{{ $anak['url'] }}" @class(['flex min-h-11 items-center rounded-lg px-3 font-bold', 'bg-brand-50 text-brand-800' => $aktif($anak['url']), 'text-slate-700' => ! $aktif($anak['url'])])>{{ $anak['label'] }}</a>
                            @endforeach
                        @else
                            <a href="{{ $item['url'] }}" @class(['flex min-h-11 items-center rounded-lg px-3 font-bold', 'bg-brand-50 text-brand-800' => $aktif($item['url']), 'text-slate-700' => ! $aktif($item['url'])])>{{ $item['label'] }}</a>
                        @endif
                    @endforeach
                </nav>
            </details>
        </div>
    </header>

    <main id="isi" class="flex-1">
        {{ $slot }}
    </main>

    <footer class="mt-20 border-t border-garis bg-white">
        <div class="wadah grid gap-8 py-10 text-sm text-slate-600 sm:grid-cols-2 lg:grid-cols-4">
            <div class="lg:col-span-2">
                <p class="text-base font-extrabold text-slate-800">{{ $namaSekolah }}</p>
                @if ($alamat = Pengaturan::alamatLengkap())
                    <p class="mt-2 max-w-md">{{ $alamat }}</p>
                @endif
                @if ($jam = Pengaturan::ambil('jam_layanan'))
                    <p class="mt-2">Layanan: {{ $jam }}</p>
                @endif
                @if ($npsn = Pengaturan::ambil('npsn'))
                    <p class="mt-2">NPSN {{ $npsn }}</p>
                @endif
            </div>
            <div>
                <p class="font-extrabold text-slate-800">Hubungi kami</p>
                <ul class="mt-2 space-y-1.5">
                    @if ($telepon = Pengaturan::ambil('telepon'))
                        <li>Telepon <a href="tel:{{ preg_replace('/[^\d+]/', '', $telepon) }}" class="tautan">{{ $telepon }}</a></li>
                    @endif
                    @if ($wa = Pengaturan::tautanWhatsapp())
                        <li>WhatsApp <a href="{{ $wa }}" class="tautan" rel="noopener" target="_blank">{{ Pengaturan::ambil('whatsapp') }}</a></li>
                    @endif
                    @if ($surel = Pengaturan::ambil('email'))
                        <li><a href="mailto:{{ $surel }}" class="tautan break-all">{{ $surel }}</a></li>
                    @endif
                    <li><a href="{{ route('kontak') }}" class="tautan">Kirim pesan</a></li>
                </ul>
                @if ($medsos = Pengaturan::mediaSosial())
                    <ul class="mt-4 flex flex-wrap gap-x-4 gap-y-1">
                        @foreach ($medsos as $nama => $url)
                            <li><a href="{{ $url }}" target="_blank" rel="noopener" class="tautan">{{ $nama }}</a></li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div>
                <p class="font-extrabold text-slate-800">Tautan</p>
                <ul class="mt-2 space-y-1.5">
                    <li><a href="{{ route('unduhan') }}" class="tautan">Unduhan</a></li>
                    <li><a href="{{ Pengaturan::ambil('tebakaksi_url') }}" class="tautan" target="_blank" rel="noopener">Aplikasi TEBAK AKSI</a></li>
                    @if ($resmi = Pengaturan::ambil('tautan_data_resmi'))
                        <li><a href="{{ $resmi }}" class="tautan" target="_blank" rel="noopener">Data resmi sekolah</a></li>
                    @endif
                    @if (Halaman::terbit()->where('slug', 'kebijakan-privasi')->exists())
                        <li><a href="{{ route('halaman', 'kebijakan-privasi') }}" class="tautan">Kebijakan privasi</a></li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="border-t border-garis">
            <p class="wadah py-4 text-xs text-slate-500">© {{ date('Y') }} {{ $namaSekolah }}</p>
        </div>
    </footer>

    {{ $kaki ?? '' }}
</body>
</html>
