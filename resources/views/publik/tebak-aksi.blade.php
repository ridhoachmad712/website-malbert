@use('App\Models\Pengaturan')

@php
    $url = Pengaturan::ambil('tebakaksi_url');
    $gambar = Pengaturan::urlBerkas('tebakaksi_gambar');
@endphp

<x-layouts.publik judul="Program TEBAK AKSI" :deskripsi="Pengaturan::ambil('tebakaksi_ringkasan')" :gambar="$gambar">
    <x-kepala-halaman judul="TEBAK AKSI" :ringkasan="Pengaturan::ambil('tebakaksi_kepanjangan')" :jejak="['Program unggulan' => route('tebakaksi')]">
        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ $url }}" target="_blank" rel="noopener" class="tombol-utama">
                Buka aplikasi TEBAK AKSI
                <x-heroicon-m-arrow-top-right-on-square class="h-4 w-4" aria-hidden="true" />
            </a>
        </div>
    </x-kepala-halaman>

    <div class="wadah mt-10 grid gap-8 lg:grid-cols-5">
        <div class="space-y-8 lg:col-span-3">
            <section class="kartu p-6 sm:p-8">
                <h2 class="judul-bagian">Tentang program</h2>
                <p class="mt-3 text-lg text-slate-700">{{ Pengaturan::ambil('tebakaksi_ringkasan') }}</p>
                @if (filled(strip_tags((string) Pengaturan::ambil('tebakaksi_isi'))))
                    <x-isi :html="Pengaturan::ambil('tebakaksi_isi')" class="mt-5" />
                @endif
            </section>

            @if ($poin = Pengaturan::daftar('tebakaksi_poin'))
                <section class="kartu p-6 sm:p-8">
                    <h2 class="judul-bagian">Cara kerja program</h2>
                    <ol class="mt-5 space-y-5">
                        @foreach ($poin as $i => $item)
                            <li class="flex gap-4">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-brand-50 font-extrabold text-brand-800">{{ $i + 1 }}</span>
                                <div>
                                    <p class="font-extrabold text-slate-900">{{ $item['judul'] }}</p>
                                    <p class="mt-1 text-slate-600">{{ $item['keterangan'] }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </section>
            @endif
        </div>

        <aside class="space-y-6 lg:col-span-2">
            @if ($gambar)
                <img src="{{ $gambar }}" alt="Kegiatan Teras Baca" class="w-full rounded-xl border border-garis object-cover">
            @endif
            <div class="kartu p-6">
                <h2 class="font-extrabold text-slate-900">Aplikasi TEBAK AKSI</h2>
                <p class="mt-2 text-sm text-slate-600">
                    Kegiatan harian, karya literasi, galeri, dan layanan pengaduan program dikelola di aplikasi TEBAK AKSI.
                    Guru mencatat kegiatan murid di sana; orang tua dan masyarakat dapat melihat halaman publiknya.
                </p>
                <a href="{{ $url }}" target="_blank" rel="noopener" class="tautan mt-4 inline-block break-all text-sm">{{ preg_replace('~^https?://~', '', $url) }}</a>
            </div>
        </aside>
    </div>
</x-layouts.publik>
