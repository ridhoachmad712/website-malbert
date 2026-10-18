@use('App\Models\Pengaturan')

@php
    $nama = Pengaturan::ambil('kepsek_nama');
    $foto = Pengaturan::urlBerkas('kepsek_foto');
    $inisial = mb_strtoupper(collect(preg_split('/\s+/', trim(preg_replace('/[^\p{L}\s]/u', '', explode(',', $nama)[0]))))->filter()->take(2)->map(fn ($kata) => mb_substr($kata, 0, 1))->implode(''));
@endphp

<div class="wadah grid items-center gap-10 md:grid-cols-[minmax(0,18rem)_1fr] lg:gap-16">
    <figure class="mx-auto w-full max-w-xs md:max-w-none">
        @if ($foto)
            <img src="{{ $foto }}" alt="Foto {{ $nama }}" loading="lazy" class="aspect-[3/4] w-full rounded-xl border border-garis object-cover">
        @else
            <div class="grid aspect-[3/4] w-full place-items-center rounded-xl bg-brand-50 text-6xl font-extrabold text-brand-800" aria-hidden="true">{{ $inisial }}</div>
        @endif
        <figcaption class="mt-4 text-center md:text-left">
            <span class="block font-extrabold text-slate-900">{{ $nama }}</span>
            <span class="block text-sm text-slate-600">Kepala {{ Pengaturan::ambil('nama_singkat') }}</span>
        </figcaption>
    </figure>

    <div>
        <x-kepala-bagian :bagian="$bagian" />
        <blockquote class="mt-6 border-l-4 border-brand-300 pl-5 text-xl leading-relaxed text-slate-700 sm:text-2xl">
            {{ Pengaturan::ambil('kepsek_sambutan_singkat') }}
        </blockquote>
        @if (filled(strip_tags((string) Pengaturan::ambil('kepsek_sambutan'))))
            <a href="{{ route('profil') }}#sambutan" class="tombol-kedua mt-8">
                Baca sambutan lengkap
                <x-heroicon-m-arrow-right class="h-4 w-4" aria-hidden="true" />
            </a>
        @endif
    </div>
</div>
