<x-layouts.publik judul="Galeri" deskripsi="Dokumentasi foto dan video kegiatan sekolah.">
    <x-kepala-halaman judul="Galeri kegiatan" :jejak="['Galeri' => route('galeri')]"
        ringkasan="Dokumentasi foto dan video kegiatan sekolah." />

    <div class="wadah mt-10">
        @if ($album->isEmpty())
            <x-kosong judul="Belum ada album foto" :tautan="route('berita')" label-tautan="Baca berita sekolah">
                Dokumentasi kegiatan akan tampil di sini setelah diunggah sekolah. Sementara itu, baca kabar kegiatan di halaman Berita.
            </x-kosong>
        @else
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                @foreach ($album as $item)
                    <x-kartu-album :album="$item" />
                @endforeach
            </div>
            <div class="mt-8">{{ $album->links() }}</div>
        @endif
    </div>
</x-layouts.publik>
