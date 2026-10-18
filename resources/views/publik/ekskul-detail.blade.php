@php
    $rincian = array_filter(['Jadwal' => $ekskul->jadwal, 'Tempat' => $ekskul->tempat, 'Pembina' => $ekskul->pembina, 'Peserta' => $ekskul->peserta]);
    $fotoAlbum = $ekskul->album?->foto ?? collect();
@endphp

<x-layouts.publik :judul="$ekskul->nama" :deskripsi="$ekskul->ringkasan" :gambar="$ekskul->urlBerkas('foto')">
    <x-kepala-halaman :judul="$ekskul->nama" :ringkasan="$ekskul->ringkasan"
        :jejak="['Profil' => route('profil'), 'Ekstrakurikuler' => route('ekskul')]" />

    <div class="wadah mt-10 grid gap-8 lg:grid-cols-3">
        <article class="space-y-6 lg:col-span-2">
            @if ($foto = $ekskul->urlBerkas('foto'))
                <img src="{{ $foto }}" alt="Kegiatan {{ $ekskul->nama }}" class="w-full rounded-xl border border-garis object-cover">
            @endif
            @if (filled(strip_tags((string) $ekskul->deskripsi)))
                <div class="kartu p-6 sm:p-8">
                    <x-isi :html="$ekskul->deskripsi" />
                </div>
            @endif

            @if ($fotoAlbum->isNotEmpty())
                <section>
                    <div class="flex items-end justify-between gap-4">
                        <h2 class="judul-bagian">Dokumentasi</h2>
                        <a href="{{ $ekskul->album->url() }}" class="tautan text-sm">Lihat album</a>
                    </div>
                    <ul class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                        @foreach ($fotoAlbum->take(9) as $item)
                            <li>
                                <a href="{{ $item->url() }}" data-penampil data-keterangan="{{ $item->keterangan }}" class="block overflow-hidden rounded-lg border border-garis">
                                    <img src="{{ $item->urlKecil('path') }}" alt="{{ $item->keterangan ?: 'Foto kegiatan '.$ekskul->nama }}" loading="lazy" class="aspect-square w-full object-cover">
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif
        </article>

        <aside class="space-y-6">
            @if ($rincian)
                <dl class="kartu divide-y divide-garis">
                    @foreach ($rincian as $label => $nilai)
                        <div class="px-5 py-4">
                            <dt class="text-sm text-slate-600">{{ $label }}</dt>
                            <dd class="mt-0.5 font-bold text-slate-900">{{ $nilai }}</dd>
                        </div>
                    @endforeach
                </dl>
            @endif
            @if ($lainnya->isNotEmpty())
                <div>
                    <h2 class="font-extrabold text-slate-900">Ekstrakurikuler lainnya</h2>
                    <nav class="mt-3 kartu p-2" aria-label="Ekstrakurikuler lainnya">
                        @foreach ($lainnya as $item)
                            <a href="{{ $item->url() }}" class="flex min-h-11 items-center rounded-lg px-3 text-sm font-bold text-slate-700 hover:bg-abu">{{ $item->nama }}</a>
                        @endforeach
                    </nav>
                </div>
            @endif
        </aside>
    </div>

    @if ($fotoAlbum->isNotEmpty())
        <x-slot:kaki>
            <x-penampil-foto />
        </x-slot:kaki>
    @endif
</x-layouts.publik>
