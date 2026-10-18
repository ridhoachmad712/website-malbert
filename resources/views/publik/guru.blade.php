<x-layouts.publik judul="Guru dan Tenaga Kependidikan">
    <x-kepala-halaman judul="Guru dan tenaga kependidikan" :jejak="['Profil' => route('profil'), 'Guru dan tendik' => route('guru')]"
        ringkasan="Para pendidik yang mendampingi murid belajar dan bertumbuh setiap hari." />

    <div class="wadah mt-10 space-y-12">
        @forelse ($kelompok as $item)
            <section>
                <h2 class="judul-bagian">{{ $item['label'] }}</h2>
                <ul class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                    @foreach ($item['guru'] as $guru)
                        <li>
                            <a href="{{ $guru->url() }}" class="kartu group block h-full p-3">
                                <x-foto-guru :guru="$guru" />
                                <span class="mt-3 block font-extrabold leading-snug text-slate-900 group-hover:text-brand-800 group-hover:underline underline-offset-4">{{ $guru->nama }}</span>
                                @if ($guru->jabatan)
                                    <span class="mt-0.5 block text-sm text-slate-600">{{ $guru->jabatan }}</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @empty
            <x-kosong judul="Data guru belum ditampilkan" :tautan="route('profil')" label-tautan="Kembali ke Profil">
                Daftar guru dan tenaga kependidikan sedang disiapkan sekolah. Sementara itu, lihat profil sekolah.
            </x-kosong>
        @endforelse
    </div>
</x-layouts.publik>
