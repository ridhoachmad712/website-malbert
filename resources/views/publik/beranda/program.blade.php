@use('App\Models\Pengaturan')

@php
    $gambar = Pengaturan::urlBerkas('tebakaksi_gambar');
    $poin = array_slice(Pengaturan::daftar('tebakaksi_poin'), 0, 6);
    $url = Pengaturan::ambil('tebakaksi_url');
@endphp

{{-- Pita gelap: satu-satunya di Beranda, menandai program unggulan --}}
<div class="wadah">
    <div @class(['grid items-center gap-10', 'lg:grid-cols-2' => $gambar])>
        <div>
            <x-kepala-bagian :bagian="$bagian" gelap />
            <p class="mt-2 font-semibold text-white/80">{{ Pengaturan::ambil('tebakaksi_kepanjangan') }}</p>
            <p class="mt-5 max-w-2xl text-lg text-white/85">{{ Pengaturan::ambil('tebakaksi_ringkasan') }}</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('tebakaksi') }}" class="tombol-utama">
                    Tentang program
                    <x-heroicon-m-arrow-right class="h-4 w-4" aria-hidden="true" />
                </a>
                @if ($url)
                    <a href="{{ $url }}" target="_blank" rel="noopener" class="tombol-terang">
                        Buka aplikasi TEBAK AKSI
                        <x-heroicon-m-arrow-top-right-on-square class="h-4 w-4" aria-hidden="true" />
                    </a>
                @endif
            </div>
        </div>
        @if ($gambar)
            <img src="{{ $gambar }}" alt="Kegiatan Teras Baca" loading="lazy" class="aspect-[4/3] w-full rounded-xl object-cover">
        @endif
    </div>

    @if ($poin)
        <ol class="mt-12 grid gap-4 border-t border-white/15 pt-10 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($poin as $i => $item)
                <li class="flex gap-4">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-white/10 text-lg font-extrabold text-white">{{ $i + 1 }}</span>
                    <div>
                        <p class="font-extrabold text-white">{{ $item['judul'] }}</p>
                        <p class="mt-1 text-sm text-white/75">{{ $item['keterangan'] }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    @endif
</div>
