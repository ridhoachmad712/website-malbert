@use('App\Models\Pengaturan')

<x-layouts.publik judul="Ekstrakurikuler" :deskripsi="Pengaturan::ambil('ekskul_pengantar')">
    <x-kepala-halaman judul="Ekstrakurikuler" :ringkasan="Pengaturan::ambil('ekskul_pengantar')"
        :jejak="['Profil' => route('profil'), 'Ekstrakurikuler' => route('ekskul')]" />

    <div class="wadah mt-10">
        @if ($ekskul->isEmpty())
            <x-kosong judul="Daftar ekstrakurikuler belum tersedia" :tautan="route('kontak')" label-tautan="Tanyakan ke sekolah">
                Kegiatan ekstrakurikuler sedang disiapkan untuk ditampilkan. Untuk informasi kegiatan, silakan hubungi sekolah.
            </x-kosong>
        @else
            <ul class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($ekskul as $item)
                    <li class="kartu group relative flex gap-4 overflow-hidden p-3 sm:flex-col sm:gap-0 sm:p-0">
                        @if ($foto = $item->urlKecil('foto'))
                            <img src="{{ $foto }}" alt="" loading="lazy" class="aspect-square w-24 shrink-0 self-start rounded-lg object-cover sm:aspect-[16/10] sm:w-full sm:rounded-none">
                        @else
                            <x-foto-pengganti class="aspect-square w-24 shrink-0 self-start rounded-lg sm:aspect-[16/10] sm:w-full sm:rounded-none" />
                        @endif
                        <div class="flex min-w-0 flex-1 flex-col py-1 sm:p-5">
                            <h2 class="text-lg font-extrabold text-slate-900">
                                <a href="{{ $item->url() }}" class="underline-offset-4 after:absolute after:inset-0 group-hover:text-brand-800 group-hover:underline">{{ $item->nama }}</a>
                            </h2>
                            @if ($item->ringkasan)
                                <p class="mt-2 text-sm text-slate-600">{{ $item->ringkasan }}</p>
                            @endif
                            @if ($item->jadwal || $item->peserta)
                                <p class="mt-auto pt-2 text-sm font-semibold text-slate-700 sm:pt-4">{{ collect([$item->jadwal, $item->peserta])->filter()->implode(' · ') }}</p>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</x-layouts.publik>
