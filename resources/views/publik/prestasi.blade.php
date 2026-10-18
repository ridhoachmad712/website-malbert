<x-layouts.publik judul="Prestasi" deskripsi="Prestasi murid, guru, dan sekolah.">
    <x-kepala-halaman judul="Prestasi" :jejak="['Prestasi' => route('prestasi')]"
        ringkasan="Penghargaan yang diraih murid, guru, dan sekolah." />

    <div class="wadah mt-10">
        @if ($prestasi->isEmpty())
            <x-kosong judul="Belum ada prestasi yang ditampilkan" :tautan="route('berita')" label-tautan="Baca berita sekolah">
                Prestasi murid, guru, dan sekolah akan tampil di sini setelah dicatat sekolah.
            </x-kosong>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($prestasi as $item)
                    <x-kartu-prestasi :prestasi="$item" />
                @endforeach
            </div>
            <div class="mt-8">{{ $prestasi->links() }}</div>
        @endif
    </div>
</x-layouts.publik>
