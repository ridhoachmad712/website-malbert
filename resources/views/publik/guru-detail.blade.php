<x-layouts.publik :judul="$guru->nama" :deskripsi="trim($guru->jabatan.'. '.$guru->motto, '. ')" :gambar="$guru->urlFoto()">
    <div class="border-b border-garis bg-white">
        <div class="wadah py-4">
            <nav aria-label="Jejak halaman" class="text-sm">
                <ol class="flex flex-wrap items-center gap-x-2 text-slate-600">
                    <li><a href="{{ route('beranda') }}" class="hover:text-slate-900 hover:underline">Beranda</a></li>
                    <li aria-hidden="true">/</li>
                    <li><a href="{{ route('profil') }}" class="hover:text-slate-900 hover:underline">Profil</a></li>
                    <li aria-hidden="true">/</li>
                    <li><a href="{{ route('guru') }}" class="hover:text-slate-900 hover:underline">Guru dan tendik</a></li>
                </ol>
            </nav>
        </div>
    </div>

    <article class="wadah mt-10 grid gap-8 md:grid-cols-3">
        <div class="mx-auto w-full max-w-56 sm:max-w-xs md:max-w-none">
            <x-foto-guru :guru="$guru" besar class="rounded-xl border border-garis" />
        </div>

        <div class="md:col-span-2">
            <h1 class="judul-halaman">{{ $guru->nama }}</h1>
            @if ($guru->jabatan)
                <p class="mt-2 text-lg font-bold text-slate-700">{{ $guru->jabatan }}</p>
            @endif
            @if ($guru->nip)
                <p class="mt-1 teks-bantu">NIP {{ $guru->nip }}</p>
            @endif

            @if ($guru->motto)
                <blockquote class="mt-6 border-l-4 border-brand-300 pl-4 text-lg italic text-slate-700">{{ $guru->motto }}</blockquote>
            @endif

            @if ($guru->profil)
                <div class="isi-konten mt-6 max-w-2xl">
                    @foreach (preg_split('/\R{2,}/', trim($guru->profil)) as $paragraf)
                        <p>{!! nl2br(e($paragraf)) !!}</p>
                    @endforeach
                </div>
            @endif

            @if ($tautan = $guru->daftarTautan())
                <div class="mt-6 flex flex-wrap gap-3">
                    @foreach ($tautan as $i => $item)
                        <a href="{{ $item['url'] }}" target="_blank" rel="noopener" @class(['tombol-utama' => $i === 0, 'tombol-kedua' => $i > 0])>
                            {{ $item['label'] }}
                            <x-heroicon-m-arrow-top-right-on-square class="h-4 w-4" aria-hidden="true" />
                        </a>
                    @endforeach
                </div>
            @endif

            @if ($medsos = $guru->daftarMediaSosial())
                <div class="mt-6">
                    <p class="text-sm font-bold text-slate-700">Media sosial</p>
                    <ul class="mt-2 flex flex-wrap gap-x-5 gap-y-2">
                        @foreach ($medsos as $item)
                            <li><a href="{{ $item['url'] }}" target="_blank" rel="noopener me" class="tautan">{{ $item['label'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <a href="{{ route('guru') }}" class="tautan mt-10 inline-block text-sm">Kembali ke daftar guru</a>
        </div>
    </article>
</x-layouts.publik>
