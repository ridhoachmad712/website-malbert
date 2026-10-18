@use('App\Models\Pengaturan')
@use('App\Support\Foto')

<x-layouts.publik judul="Sarana dan Prasarana" :deskripsi="Pengaturan::ambil('sarana_pengantar')">
    <x-kepala-halaman judul="Sarana dan prasarana" :ringkasan="Pengaturan::ambil('sarana_pengantar')"
        :jejak="['Profil' => route('profil'), 'Sarana dan prasarana' => route('sarana')]" />

    <div class="wadah mt-10">
        @if ($sarana->isEmpty())
            <x-kosong judul="Data sarana dan prasarana belum tersedia" :tautan="route('galeri')" label-tautan="Lihat galeri sekolah">
                Foto ruang belajar dan fasilitas sekolah akan tampil di sini. Sementara itu, lihat dokumentasi kegiatan di Galeri.
            </x-kosong>
        @else
            <ul class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($sarana as $item)
                    @php $foto = $item->daftarFoto(); @endphp
                    <li class="kartu flex flex-col overflow-hidden">
                        @if ($foto)
                            <a href="{{ Foto::url($foto[0]) }}" data-penampil data-keterangan="{{ $item->nama }}" class="relative block">
                                <img src="{{ Foto::kecil($foto[0]) }}" alt="{{ $item->nama }}" loading="lazy" class="aspect-[4/3] w-full object-cover">
                                @if (count($foto) > 1)
                                    <span class="absolute bottom-3 right-3 rounded-md bg-slate-950/70 px-2 py-1 text-xs font-bold text-white">{{ count($foto) }} foto</span>
                                @endif
                            </a>
                            {{-- Foto lain tetap bisa dibuka di penampil, tanpa memenuhi kartu --}}
                            @foreach (array_slice($foto, 1) as $lain)
                                <a href="{{ Foto::url($lain) }}" data-penampil data-keterangan="{{ $item->nama }}" class="sr-only">Foto {{ $item->nama }} {{ $loop->iteration + 1 }}</a>
                            @endforeach
                        @else
                            <x-foto-pengganti :teks="$item->nama" class="aspect-[4/3] w-full" />
                        @endif
                        <div class="flex flex-1 flex-col p-5">
                            <h2 class="text-lg font-extrabold text-slate-900">{{ $item->nama }}</h2>
                            @if ($item->jumlah)
                                <p class="mt-1 text-sm font-bold text-brand-800">{{ $item->jumlah }}</p>
                            @endif
                            @if ($item->keterangan)
                                <p class="mt-2 text-sm text-slate-600">{{ $item->keterangan }}</p>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    @if ($sarana->contains(fn ($item) => $item->daftarFoto()))
        <x-slot:kaki>
            <x-penampil-foto />
        </x-slot:kaki>
    @endif
</x-layouts.publik>
