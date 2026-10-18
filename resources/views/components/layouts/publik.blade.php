@props(['judul' => null, 'deskripsi' => null, 'gambar' => null])

@use('App\Models\Pengaturan')
@use('App\Support\Menu')

@php
    $namaSekolah = Pengaturan::ambil('nama_sekolah');
    $deskripsi ??= Pengaturan::ambil('beranda_paragraf');
    $gambar ??= Pengaturan::urlBerkas('beranda_foto') ?? Pengaturan::urlBerkas('logo_sekolah');

    // Menu, tombol kepala, dan tautan kaki halaman diatur admin di Pengaturan, Menu & Tombol
    $menuUtama = Menu::utama();
    $tombolKepala = Menu::tombolKepala();
    $tautanKaki = Menu::kaki();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ffffff">
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

            <div class="hidden h-full items-stretch gap-1 lg:flex">
                <nav class="flex h-full items-stretch gap-1" aria-label="Menu utama">
                    @foreach ($menuUtama as $item)
                        @if ($item['anak'])
                            <details data-menu class="relative flex">
                                <summary @class(['flex cursor-pointer items-center gap-1 border-b-2 px-3 text-sm font-bold',
                                    'border-brand-700 text-brand-800' => $item['aktif'],
                                    'border-transparent text-slate-600 hover:text-slate-900' => ! $item['aktif']])>
                                    {{ $item['label'] }}
                                    <x-heroicon-m-chevron-down class="h-4 w-4" aria-hidden="true" />
                                </summary>
                                <div class="menu-turun">
                                    @foreach ($item['anak'] as $anak)
                                        <a href="{{ $anak['url'] }}" @if ($anak['aktif']) aria-current="page" @endif @if ($anak['keluar']) target="_blank" rel="noopener" @endif>
                                            {{ $anak['label'] }}
                                            @if ($anak['eksternal'])
                                                <x-heroicon-m-arrow-top-right-on-square class="ml-auto h-4 w-4 text-slate-500" aria-label="(tautan luar)" />
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </details>
                        @else
                            <a href="{{ $item['url'] }}" @if ($item['aktif']) aria-current="page" @endif @if ($item['keluar']) target="_blank" rel="noopener" @endif
                               @class(['flex items-center gap-1 whitespace-nowrap border-b-2 px-3 text-sm font-bold',
                                   'border-brand-700 text-brand-800' => $item['aktif'],
                                   'border-transparent text-slate-600 hover:text-slate-900' => ! $item['aktif']])>
                                {{ $item['label'] }}
                                @if ($item['eksternal'])
                                    <x-heroicon-m-arrow-top-right-on-square class="h-4 w-4" aria-label="(tautan luar)" />
                                @endif
                            </a>
                        @endif
                    @endforeach
                </nav>
                @if ($tombolKepala)
                    <span class="ml-3 flex items-center">
                        <a href="{{ $tombolKepala['url'] }}" class="tombol-utama whitespace-nowrap" @if ($tombolKepala['keluar']) target="_blank" rel="noopener" @endif>{{ $tombolKepala['label'] }}</a>
                    </span>
                @endif
            </div>

            <details data-menu class="group lg:hidden">
                <summary class="flex min-h-11 cursor-pointer items-center gap-2 rounded-lg px-3 text-sm font-bold text-slate-700 hover:bg-slate-50">
                    <x-heroicon-o-bars-3 class="h-6 w-6 group-open:hidden" aria-hidden="true" />
                    <x-heroicon-o-x-mark class="hidden h-6 w-6 group-open:block" aria-hidden="true" />
                    <span class="group-open:hidden">Menu</span>
                    <span class="hidden group-open:inline">Tutup</span>
                </summary>
                <nav class="absolute inset-x-0 top-16 max-h-[calc(100dvh-4rem)] overflow-y-auto border-b border-garis bg-white px-4 pb-4 pt-2 shadow-lg" aria-label="Menu utama">
                    @foreach ($menuUtama as $item)
                        @if ($item['anak'])
                            <p class="mt-3 px-3 pb-1 text-xs font-bold text-slate-500">{{ $item['label'] }}</p>
                            @foreach ($item['anak'] as $anak)
                                <a href="{{ $anak['url'] }}" @if ($anak['keluar']) target="_blank" rel="noopener" @endif
                                   @class(['flex min-h-11 items-center rounded-lg px-3 font-bold', 'bg-brand-50 text-brand-800' => $anak['aktif'], 'text-slate-700' => ! $anak['aktif']])>{{ $anak['label'] }}</a>
                            @endforeach
                        @else
                            <a href="{{ $item['url'] }}" @if ($item['keluar']) target="_blank" rel="noopener" @endif
                               @class(['flex min-h-11 items-center rounded-lg px-3 font-bold', 'bg-brand-50 text-brand-800' => $item['aktif'], 'text-slate-700' => ! $item['aktif']])>{{ $item['label'] }}</a>
                        @endif
                    @endforeach
                    @if ($tombolKepala)
                        <a href="{{ $tombolKepala['url'] }}" class="tombol-utama mt-3 w-full" @if ($tombolKepala['keluar']) target="_blank" rel="noopener" @endif>{{ $tombolKepala['label'] }}</a>
                    @endif
                </nav>
            </details>
        </div>
    </header>

    <main id="isi" class="flex-1">
        {{ $slot }}
    </main>

    <footer class="mt-20 border-t border-garis bg-white">
        <div class="wadah grid gap-8 py-12 text-sm text-slate-600 sm:grid-cols-2 lg:grid-cols-4">
            <div class="lg:col-span-2">
                <x-logo />
                @if ($kalimat = Pengaturan::ambil('kaki_deskripsi'))
                    <p class="mt-4 max-w-md text-slate-700">{{ $kalimat }}</p>
                @endif
                @if ($alamat = Pengaturan::alamatLengkap())
                    <p class="mt-4 max-w-md">{{ $alamat }}</p>
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
                <ul class="mt-3 space-y-1.5">
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
            @if ($tautanKaki)
                <div>
                    <p class="font-extrabold text-slate-800">Tautan</p>
                    <ul class="mt-3 space-y-1.5">
                        @foreach ($tautanKaki as $item)
                            <li><a href="{{ $item['url'] }}" class="tautan" @if ($item['keluar']) target="_blank" rel="noopener" @endif>{{ $item['label'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        <div class="border-t border-garis">
            <p class="wadah py-4 text-xs text-slate-500">© {{ date('Y') }} {{ $namaSekolah }}</p>
        </div>
    </footer>

    {{ $kaki ?? '' }}
</body>
</html>
