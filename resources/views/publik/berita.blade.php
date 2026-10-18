@use('App\Models\Berita')

<x-layouts.publik :judul="$kategori ? Berita::KATEGORI[$kategori] : 'Berita'">
    <x-kepala-halaman :judul="$kategori === 'pengumuman' ? 'Pengumuman' : 'Berita dan pengumuman'" :jejak="['Berita' => route('berita')]">
        <nav class="-mx-4 mt-6 tanpa-bilah flex gap-2 overflow-x-auto px-4 pb-1 sm:mx-0 sm:flex-wrap sm:overflow-visible sm:px-0" aria-label="Kategori berita">
            <a href="{{ route('berita') }}" @class(['tab', 'tab-aktif' => ! $kategori, 'tab-pasif' => $kategori]) @if (! $kategori) aria-current="page" @endif>Semua</a>
            @foreach (Berita::KATEGORI as $kunci => $label)
                <a href="{{ route('berita', ['kategori' => $kunci]) }}" @class(['tab', 'tab-aktif' => $kategori === $kunci, 'tab-pasif' => $kategori !== $kunci])
                   @if ($kategori === $kunci) aria-current="page" @endif>{{ $label }}</a>
            @endforeach
        </nav>
    </x-kepala-halaman>

    <div class="wadah mt-10">
        @if ($berita->isEmpty())
            <x-kosong :judul="$kategori ? 'Belum ada '.mb_strtolower(Berita::KATEGORI[$kategori]).' yang terbit' : 'Belum ada berita yang terbit'"
                :tautan="$kategori ? route('berita') : route('agenda')" :label-tautan="$kategori ? 'Lihat semua berita' : 'Lihat agenda sekolah'">
                Tulisan baru akan tampil di sini setelah diterbitkan sekolah.
            </x-kosong>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($berita as $item)
                    <x-kartu-berita :berita="$item" />
                @endforeach
            </div>
            <div class="mt-8">{{ $berita->links() }}</div>
        @endif
    </div>
</x-layouts.publik>
