<x-layouts.publik :judul="$berita->judul" :deskripsi="$berita->cuplikan(200)" :gambar="$berita->urlBerkas('gambar')">
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
                    · <time datetime="{{ $berita->terbit_pada->toIso8601String() }}">{{ $berita->terbit_pada->translatedFormat('l, j F Y') }}</time>
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

            <div class="mt-10 flex flex-wrap items-center gap-3 border-t border-garis pt-6">
                <span class="text-sm font-bold text-slate-700">Bagikan:</span>
                <a href="https://wa.me/?text={{ rawurlencode($berita->judul.' '.$berita->url()) }}" target="_blank" rel="noopener" class="tombol-kedua">WhatsApp</a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode($berita->url()) }}" target="_blank" rel="noopener" class="tombol-kedua">Facebook</a>
            </div>
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
