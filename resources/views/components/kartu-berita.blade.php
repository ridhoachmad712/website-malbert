@props(['berita'])

<article class="kartu group relative flex flex-col overflow-hidden">
    @if ($gambar = $berita->urlBerkas('gambar'))
        <img src="{{ $gambar }}" alt="{{ $berita->keterangan_gambar ?: '' }}" loading="lazy" class="aspect-[16/10] w-full object-cover">
    @endif
    <div class="flex flex-1 flex-col p-5">
        <p class="flex flex-wrap items-center gap-2 text-xs font-bold text-slate-600">
            <span @class(['lencana', 'lencana-aksen' => $berita->kategori === 'pengumuman'])>{{ $berita->namaKategori() }}</span>
            <time datetime="{{ $berita->terbit_pada->toDateString() }}">{{ $berita->terbit_pada->translatedFormat('j F Y') }}</time>
        </p>
        <h3 class="mt-3 text-lg font-extrabold leading-snug text-slate-900">
            <a href="{{ $berita->url() }}" class="after:absolute after:inset-0 group-hover:text-brand-800 group-hover:underline underline-offset-4">{{ $berita->judul }}</a>
        </h3>
        <p class="mt-2 line-clamp-3 text-sm text-slate-600">{{ $berita->cuplikan() }}</p>
    </div>
</article>
