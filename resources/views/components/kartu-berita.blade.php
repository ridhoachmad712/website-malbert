@props(['berita'])

{{-- Di HP kartu berbentuk baris (foto kecil di kiri) agar lebih banyak berita terlihat; di layar lebar berbentuk kartu tegak --}}
<article class="kartu group relative flex gap-4 overflow-hidden p-3 sm:flex-col sm:gap-0 sm:p-0">
    @if ($gambar = $berita->urlKecil('gambar'))
        <img src="{{ $gambar }}" alt="{{ $berita->keterangan_gambar ?: '' }}" loading="lazy" class="aspect-square w-24 shrink-0 rounded-lg object-cover sm:aspect-[16/10] sm:w-full sm:rounded-none">
    @else
        <x-foto-pengganti :teks="$berita->namaKategori()" class="aspect-square w-24 shrink-0 rounded-lg sm:aspect-[16/10] sm:w-full sm:rounded-none" />
    @endif
    <div class="flex min-w-0 flex-1 flex-col py-1 sm:p-5">
        <p class="flex flex-wrap items-center gap-2 text-xs font-bold text-slate-600">
            <span @class(['lencana', 'lencana-aksen' => $berita->kategori === 'pengumuman'])>{{ $berita->namaKategori() }}</span>
            <time datetime="{{ $berita->terbit_pada->toDateString() }}">{{ $berita->terbit_pada->translatedFormat('j F Y') }}</time>
        </p>
        <h3 class="mt-2 line-clamp-3 font-extrabold leading-snug text-slate-900 sm:mt-3 sm:text-lg">
            <a href="{{ $berita->url() }}" class="underline-offset-4 after:absolute after:inset-0 group-hover:text-brand-800 group-hover:underline">{{ $berita->judul }}</a>
        </h3>
        <p class="mt-2 hidden line-clamp-3 text-sm text-slate-600 sm:block">{{ $berita->cuplikan() }}</p>
    </div>
</article>
