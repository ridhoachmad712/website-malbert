@php
    $utama = $berita->first();
    $lainnya = $berita->slice(1);
@endphp

<div class="wadah">
    <x-kepala-bagian :bagian="$bagian" :tautan="route('berita')" label-tautan="Semua berita" />

    @if (! $utama)
        <x-kosong judul="Belum ada berita yang terbit" class="mt-8" :tautan="route('agenda')" label-tautan="Lihat agenda sekolah">
            Kabar kegiatan sekolah akan tampil di sini. Sementara itu, lihat agenda kegiatan yang akan datang.
        </x-kosong>
    @else
        <div class="mt-8 grid gap-6 lg:grid-cols-5">
            {{-- Berita utama --}}
            <article class="kartu group relative flex flex-col overflow-hidden lg:col-span-3">
                @if ($gambar = $utama->urlBerkas('gambar'))
                    <img src="{{ $gambar }}" alt="{{ $utama->keterangan_gambar ?: '' }}" class="aspect-[16/9] w-full object-cover">
                @else
                    <x-foto-pengganti :teks="$utama->namaKategori()" class="aspect-[16/9] w-full" />
                @endif
                <div class="flex flex-1 flex-col p-6 sm:p-7">
                    <p class="flex flex-wrap items-center gap-2 text-xs font-bold text-slate-600">
                        <span @class(['lencana', 'lencana-aksen' => $utama->kategori === 'pengumuman'])>{{ $utama->namaKategori() }}</span>
                        <time datetime="{{ $utama->terbit_pada->toDateString() }}">{{ $utama->terbit_pada->translatedFormat('j F Y') }}</time>
                    </p>
                    <h3 class="mt-3 text-2xl font-extrabold leading-snug text-slate-900">
                        <a href="{{ $utama->url() }}" class="underline-offset-4 after:absolute after:inset-0 group-hover:text-brand-800 group-hover:underline">{{ $utama->judul }}</a>
                    </h3>
                    <p class="mt-3 text-slate-600">{{ $utama->cuplikan(220) }}</p>
                    <p class="mt-4 text-sm text-slate-600">Oleh {{ $utama->namaPenulis() }}</p>
                </div>
            </article>

            {{-- Berita pendamping --}}
            @if ($lainnya->isNotEmpty())
                <ul class="flex flex-col gap-4 lg:col-span-2">
                    @foreach ($lainnya as $item)
                        <li class="kartu group relative flex gap-4 overflow-hidden p-3">
                            @if ($gambar = $item->urlKecil('gambar'))
                                <img src="{{ $gambar }}" alt="" loading="lazy" class="aspect-square w-24 shrink-0 self-start rounded-lg object-cover sm:w-28">
                            @else
                                <x-foto-pengganti class="aspect-square w-24 shrink-0 self-start rounded-lg sm:w-28" />
                            @endif
                            <div class="min-w-0 py-1">
                                <p class="text-xs font-bold text-slate-600">
                                    {{ $item->namaKategori() }} · <time datetime="{{ $item->terbit_pada->toDateString() }}">{{ $item->terbit_pada->translatedFormat('j M Y') }}</time>
                                </p>
                                <h3 class="mt-1 line-clamp-3 font-extrabold leading-snug text-slate-900">
                                    <a href="{{ $item->url() }}" class="underline-offset-4 after:absolute after:inset-0 group-hover:text-brand-800 group-hover:underline">{{ $item->judul }}</a>
                                </h3>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif
</div>
