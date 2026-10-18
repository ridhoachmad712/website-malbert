<x-layouts.publik judul="Agenda" deskripsi="Jadwal kegiatan dan hari libur sekolah.">
    <x-kepala-halaman judul="Agenda sekolah" :jejak="['Agenda' => route('agenda')]"
        ringkasan="Jadwal kegiatan dan hari libur yang akan datang." />

    <div class="wadah mt-10 max-w-4xl space-y-10">
        @forelse ($mendatang as $bulan => $daftar)
            <section>
                <h2 class="judul-bagian">{{ $bulan }}</h2>
                <ul class="mt-4 kartu divide-y divide-garis">
                    @foreach ($daftar as $item)
                        <li class="flex gap-4 p-4 sm:p-5">
                            <span class="w-16 shrink-0 rounded-lg border border-garis py-2 text-center leading-tight">
                                <span class="block text-2xl font-extrabold text-slate-900">{{ $item->mulai->format('j') }}</span>
                                <span class="block text-xs font-bold text-slate-600">{{ $item->mulai->translatedFormat('D') }}</span>
                            </span>
                            <div class="min-w-0">
                                <p class="flex flex-wrap items-center gap-2">
                                    <span class="font-extrabold text-slate-900">{{ $item->judul }}</span>
                                    @if ($item->libur)
                                        <span class="lencana-aksen">Libur</span>
                                    @endif
                                </p>
                                <p class="mt-1 text-sm text-slate-600">
                                    {{ collect([$item->tanggalTeks(), $item->waktu, $item->tempat])->filter()->implode(' · ') }}
                                </p>
                                @if ($item->keterangan)
                                    <p class="mt-2 text-sm text-slate-700">{{ $item->keterangan }}</p>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>
        @empty
            <x-kosong judul="Belum ada agenda yang akan datang" :tautan="route('berita', ['kategori' => 'pengumuman'])" label-tautan="Lihat pengumuman">
                Jadwal kegiatan dan hari libur akan tampil di sini setelah diumumkan sekolah.
            </x-kosong>
        @endforelse

        @if ($lewat->isNotEmpty())
            <details class="kartu">
                <summary class="flex min-h-11 cursor-pointer items-center justify-between px-5 py-3 font-bold text-slate-700">
                    Agenda yang sudah lewat
                    <x-heroicon-m-chevron-down class="h-5 w-5" aria-hidden="true" />
                </summary>
                <ul class="divide-y divide-garis border-t border-garis">
                    @foreach ($lewat as $item)
                        <li class="px-5 py-3 text-sm">
                            <span class="font-bold text-slate-800">{{ $item->judul }}</span>
                            <span class="block text-slate-600">{{ $item->tanggalTeks() }}</span>
                        </li>
                    @endforeach
                </ul>
            </details>
        @endif
    </div>
</x-layouts.publik>
