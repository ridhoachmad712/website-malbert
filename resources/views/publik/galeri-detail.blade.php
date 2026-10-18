<x-layouts.publik :judul="$album->judul" :deskripsi="$album->keterangan ?: 'Dokumentasi kegiatan: '.$album->judul" :gambar="$album->sampul?->url()">
    <x-kepala-halaman :judul="$album->judul" :ringkasan="$album->keterangan" :jejak="['Galeri' => route('galeri')]">
        <p class="mt-3 text-sm text-slate-600">
            {{ collect([$album->tanggal?->translatedFormat('l, j F Y'), $album->foto->count().' foto'])->filter()->implode(' · ') }}
        </p>
    </x-kepala-halaman>

    <div class="wadah mt-10 space-y-8">
        @if ($kode = $album->kodeYoutube())
            <div class="aspect-video w-full max-w-4xl overflow-hidden rounded-xl border border-garis bg-slate-900">
                <iframe src="https://www.youtube-nocookie.com/embed/{{ $kode }}" title="Video {{ $album->judul }}" class="h-full w-full"
                        loading="lazy" allow="encrypted-media; picture-in-picture" allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe>
            </div>
        @endif

        @if ($album->foto->isEmpty() && ! $kode)
            <x-kosong judul="Album ini belum berisi foto" :tautan="route('galeri')" label-tautan="Lihat album lainnya">
                Foto kegiatan sedang disiapkan. Silakan lihat album lain di Galeri.
            </x-kosong>
        @else
            <ul class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                @foreach ($album->foto as $foto)
                    <li>
                        <a href="{{ $foto->url() }}" data-penampil data-keterangan="{{ $foto->keterangan }}" class="block overflow-hidden rounded-lg border border-garis">
                            <img src="{{ $foto->urlKecil('path') }}" alt="{{ $foto->keterangan ?: 'Foto '.($loop->iteration).' kegiatan '.$album->judul }}" loading="lazy"
                                 class="aspect-square w-full object-cover transition-opacity hover:opacity-90">
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif

        <a href="{{ route('galeri') }}" class="tautan inline-block text-sm">Kembali ke Galeri</a>
    </div>

    <x-slot:kaki>
        <x-penampil-foto />
    </x-slot:kaki>
</x-layouts.publik>
