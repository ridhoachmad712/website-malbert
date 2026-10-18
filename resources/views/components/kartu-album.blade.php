@props(['album'])

<a href="{{ $album->url() }}" class="kartu group block overflow-hidden">
    @if ($album->sampul)
        <img src="{{ $album->sampul->url() }}" alt="" loading="lazy" class="aspect-[4/3] w-full object-cover">
    @elseif ($kode = $album->kodeYoutube())
        <img src="https://i.ytimg.com/vi/{{ $kode }}/hqdefault.jpg" alt="" loading="lazy" class="aspect-[4/3] w-full object-cover">
    @endif
    <div class="p-4">
        <p class="font-extrabold leading-snug text-slate-900 group-hover:text-brand-800 group-hover:underline underline-offset-4">{{ $album->judul }}</p>
        <p class="mt-1 text-sm text-slate-600">
            {{ $album->tanggal?->translatedFormat('j F Y') }}{{ $album->tanggal ? ' · ' : '' }}{{ $album->foto_count }} foto{{ $album->kodeYoutube() ? ', 1 video' : '' }}
        </p>
    </div>
</a>
