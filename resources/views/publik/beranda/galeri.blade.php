{{-- Mosaik galeri: album terbaru besar di kiri, album lain lebih kecil di kanan --}}
<div class="wadah">
    <x-kepala-bagian :bagian="$bagian" :tautan="route('galeri')" label-tautan="Semua album" />

    @php $mosaik = $album->count() >= 3; @endphp
    <ul @class(['mt-8 grid gap-4', 'grid-cols-2 lg:grid-cols-4 lg:grid-rows-2' => $mosaik, 'sm:grid-cols-2 lg:grid-cols-3' => ! $mosaik])>
        @foreach ($album as $item)
            @php $besar = $mosaik && $loop->first; @endphp
            <li @class(['col-span-2 lg:row-span-2' => $besar])>
                <a href="{{ $item->url() }}" class="group relative block h-full min-h-40 overflow-hidden rounded-xl border border-garis bg-slate-200">
                    <img src="{{ $besar ? $item->sampul->url() : $item->sampul->urlKecil('path') }}" alt="" loading="lazy"
                         @class(['h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.02]', 'aspect-[4/3]' => ! $besar, 'aspect-[4/3] lg:aspect-auto' => $besar])>
                    <span class="absolute inset-x-0 bottom-0 bg-slate-950/70 px-4 py-3 text-white">
                        <span @class(['block font-extrabold leading-snug', 'text-lg' => $besar || ! $mosaik, 'text-sm' => $mosaik && ! $besar])>{{ $item->judul }}</span>
                        <span class="block text-xs text-white/80">{{ $item->tanggal?->translatedFormat('j F Y') }}{{ $item->tanggal ? ' · ' : '' }}{{ $item->foto_count }} foto</span>
                    </span>
                </a>
            </li>
        @endforeach
    </ul>
</div>
