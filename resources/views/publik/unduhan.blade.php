<x-layouts.publik judul="Unduhan" deskripsi="Kalender pendidikan, brosur, dan formulir sekolah.">
    <x-kepala-halaman judul="Unduhan" :jejak="['Unduhan' => route('unduhan')]"
        ringkasan="Kalender pendidikan, brosur, dan formulir yang bisa diunduh." />

    <div class="wadah mt-10 max-w-4xl space-y-10">
        @forelse ($kelompok as $kategori => $daftar)
            <section>
                <h2 class="judul-bagian">{{ $kategori }}</h2>
                <ul class="mt-4 kartu divide-y divide-garis">
                    @foreach ($daftar as $item)
                        <li class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
                            <div class="min-w-0">
                                <p class="font-extrabold text-slate-900">{{ $item->judul }}</p>
                                @if ($item->keterangan)
                                    <p class="mt-1 text-sm text-slate-600">{{ $item->keterangan }}</p>
                                @endif
                                <p class="mt-1 text-xs font-bold text-slate-500">{{ collect([$item->jenisBerkas(), $item->ukuranTeks()])->filter()->implode(' · ') }}</p>
                            </div>
                            <a href="{{ $item->url() }}" download="{{ $item->nama_berkas ?: null }}" class="tombol-kedua shrink-0">
                                <x-heroicon-o-arrow-down-tray class="h-4 w-4" aria-hidden="true" /> Unduh
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @empty
            <x-kosong judul="Belum ada berkas untuk diunduh" :tautan="route('kontak')" label-tautan="Hubungi sekolah">
                Kalender pendidikan dan formulir akan tersedia di sini. Bila memerlukan dokumen tertentu, hubungi sekolah.
            </x-kosong>
        @endforelse
    </div>
</x-layouts.publik>
