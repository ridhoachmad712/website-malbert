@use('App\Models\Pengaturan')
@use('Illuminate\Support\Str')

<x-layouts.publik :judul="$berita->judul" :deskripsi="$berita->cuplikan(200)" :gambar="$berita->urlBerkas('gambar')">
    <x-slot:kepala>
        @if ($pratinjau ?? false)
            <meta name="robots" content="noindex, nofollow">
        @else
            {{-- Data terstruktur agar berita tampil rapi di hasil pencarian Google --}}
            <script type="application/ld+json">{!! json_encode(array_filter([
                '@context' => 'https://schema.org',
                '@type' => 'NewsArticle',
                'headline' => Str::limit($berita->judul, 110, ''),
                'description' => $berita->cuplikan(200),
                'image' => $berita->urlBerkas('gambar') ? [$berita->urlBerkas('gambar')] : null,
                'datePublished' => $berita->terbit_pada?->toIso8601String(),
                'dateModified' => $berita->updated_at?->toIso8601String(),
                'author' => ['@type' => $berita->penulis ? 'Person' : 'Organization', 'name' => $berita->penulis ?: Pengaturan::ambil('nama_sekolah')],
                'publisher' => array_filter(['@type' => 'Organization', 'name' => Pengaturan::ambil('nama_sekolah'), 'logo' => Pengaturan::urlBerkas('logo_sekolah') ? ['@type' => 'ImageObject', 'url' => Pengaturan::urlBerkas('logo_sekolah')] : null]),
                'mainEntityOfPage' => $berita->url(),
            ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
        @endif
    </x-slot:kepala>

    @if ($pratinjau ?? false)
        @php
            $statusPratinjau = match (true) {
                $berita->status !== 'terbit' => 'berita ini masih draf dan belum terlihat pengunjung.',
                $berita->terbit_pada?->isFuture() => 'berita ini terjadwal terbit '.$berita->terbit_pada->translatedFormat('j F Y, H.i').'.',
                default => 'berita ini sudah terbit.',
            };
        @endphp
        <div class="sticky top-16 z-20 border-b border-amber-200 bg-amber-50" role="status">
            <p class="wadah py-3 text-sm font-bold text-amber-900">Pratinjau: {{ $statusPratinjau }}</p>
        </div>
    @endif

    <article>
        <header class="border-b border-garis bg-white">
            <div class="wadah max-w-3xl py-8 sm:py-10">
                <nav aria-label="Jejak halaman" class="mb-4 text-sm">
                    <ol class="flex flex-wrap items-center gap-x-2 text-slate-600">
                        <li><a href="{{ route('beranda') }}" class="hover:text-slate-900 hover:underline">Beranda</a></li>
                        <li aria-hidden="true">/</li>
                        <li><a href="{{ route('berita') }}" class="hover:text-slate-900 hover:underline">Berita</a></li>
                        <li aria-hidden="true">/</li>
                        <li><a href="{{ route('berita', ['kategori' => $berita->kategori]) }}" class="hover:text-slate-900 hover:underline">{{ $berita->namaKategori() }}</a></li>
                    </ol>
                </nav>
                <h1 class="judul-halaman">{{ $berita->judul }}</h1>
                <p class="mt-4 text-sm text-slate-600">
                    Ditulis oleh <b class="text-slate-800">{{ $berita->namaPenulis() }}</b>
                    @if ($berita->terbit_pada)
                        · <time datetime="{{ $berita->terbit_pada->toIso8601String() }}">{{ $berita->terbit_pada->translatedFormat('l, j F Y') }}</time>
                    @endif
                </p>
            </div>
        </header>

        <div class="wadah max-w-3xl mt-8">
            @if ($gambar = $berita->urlBerkas('gambar'))
                <figure class="mb-8">
                    <img src="{{ $gambar }}" alt="{{ $berita->keterangan_gambar ?: '' }}" class="w-full rounded-xl border border-garis">
                    @if ($berita->keterangan_gambar)
                        <figcaption class="mt-2 text-sm text-slate-600">{{ $berita->keterangan_gambar }}</figcaption>
                    @endif
                </figure>
            @endif

            <x-isi :html="$berita->isi" />

            @unless ($pratinjau ?? false)
            <div class="mt-10 flex flex-wrap items-center gap-3 border-t border-garis pt-6">
                <span class="text-sm font-bold text-slate-700">Bagikan:</span>
                <a href="https://wa.me/?text={{ rawurlencode($berita->judul.' '.$berita->url()) }}" target="_blank" rel="noopener" class="tombol-kedua">WhatsApp</a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode($berita->url()) }}" target="_blank" rel="noopener" class="tombol-kedua">Facebook</a>
            </div>
            @endunless
        </div>
    </article>

    @if ($lainnya->isNotEmpty())
        <section class="wadah mt-14">
            <h2 class="judul-bagian">Berita lainnya</h2>
            <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($lainnya as $item)
                    <x-kartu-berita :berita="$item" />
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.publik>
