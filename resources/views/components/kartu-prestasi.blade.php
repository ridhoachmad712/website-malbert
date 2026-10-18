@props(['prestasi'])

<article class="kartu flex flex-col overflow-hidden">
    @if ($foto = $prestasi->urlBerkas('foto'))
        <img src="{{ $foto }}" alt="" loading="lazy" class="aspect-[16/10] w-full object-cover">
    @else
        <x-foto-pengganti :teks="'Tingkat '.$prestasi->namaTingkat()" class="aspect-[16/10] w-full" />
    @endif
    <div class="flex flex-1 flex-col p-5">
        <p class="flex flex-wrap items-center gap-2">
            @if ($prestasi->peringkat)
                <span class="lencana-aksen">{{ $prestasi->peringkat }}</span>
            @endif
            <span class="lencana">Tingkat {{ $prestasi->namaTingkat() }}</span>
        </p>
        <h3 class="mt-3 text-lg font-extrabold leading-snug text-slate-900">{{ $prestasi->judul }}</h3>
        <p class="mt-1 font-semibold text-slate-700">{{ $prestasi->peraih }}</p>
        <p class="mt-2 text-sm text-slate-600">
            {{ collect([$prestasi->penyelenggara, $prestasi->tanggal?->translatedFormat('j F Y')])->filter()->implode(' · ') }}
        </p>
        @if ($prestasi->keterangan)
            <p class="mt-2 text-sm text-slate-600">{{ $prestasi->keterangan }}</p>
        @endif
    </div>
</article>
