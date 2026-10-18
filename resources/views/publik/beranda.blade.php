@use('App\Models\Pengaturan')

@php
    $foto = Pengaturan::urlBerkas('beranda_foto');
    $spmbDibuka = Pengaturan::aktif('spmb_tampil') && Pengaturan::ambil('spmb_status') === 'dibuka';
    $angka = array_filter([
        'Akreditasi' => Pengaturan::ambil('akreditasi'),
        'Rombongan belajar' => Pengaturan::ambil('jumlah_rombel'),
        'Murid' => Pengaturan::ambil('jumlah_murid'),
        'Guru dan tendik' => $jumlahGuru ?: null,
    ], 'filled');
    $kepsek = Pengaturan::ambil('kepsek_nama');
    $sambutan = Pengaturan::ambil('kepsek_sambutan_singkat');
    $tampilSambutan = Pengaturan::aktif('beranda_tampil_sambutan') && filled($kepsek) && filled($sambutan);
    $tampilAgenda = Pengaturan::aktif('beranda_tampil_agenda');
@endphp

<x-layouts.publik>
    <x-slot:kepala>
        <script type="application/ld+json">{!! json_encode(array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'ElementarySchool',
            'name' => Pengaturan::ambil('nama_sekolah'),
            'alternateName' => Pengaturan::ambil('nama_singkat'),
            'url' => route('beranda'),
            'logo' => Pengaturan::urlBerkas('logo_sekolah'),
            'telephone' => Pengaturan::ambil('telepon') ?: null,
            'email' => Pengaturan::ambil('email') ?: null,
            'address' => Pengaturan::alamatLengkap() ?: null,
            'sameAs' => array_values(Pengaturan::mediaSosial()) ?: null,
        ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
    </x-slot:kepala>

    @if ($disematkan->isNotEmpty())
        <section aria-label="Pengumuman penting" class="border-b border-garis bg-brand-50">
            <div class="wadah divide-y divide-brand-100">
                @foreach ($disematkan as $item)
                    <p class="flex flex-col gap-1 py-3 sm:flex-row sm:items-center sm:gap-3">
                        <span class="lencana w-fit bg-brand-700 text-white">Pengumuman</span>
                        <a href="{{ $item->url() }}" class="font-bold text-brand-900 underline-offset-4 hover:underline">{{ $item->judul }}</a>
                    </p>
                @endforeach
            </div>
        </section>
    @endif

    <section class="wadah grid items-center gap-8 py-10 sm:py-14 lg:grid-cols-2 lg:gap-12">
        <div>
            <p class="font-bold text-brand-800">{{ Pengaturan::ambil('nama_sekolah') }}</p>
            <h1 class="mt-3 text-4xl font-extrabold leading-tight tracking-tight text-slate-900 sm:text-5xl">{{ Pengaturan::ambil('beranda_judul') }}</h1>
            <p class="mt-5 max-w-xl text-lg text-slate-600">{{ Pengaturan::ambil('beranda_paragraf') }}</p>
            <div class="mt-7 flex flex-wrap gap-3">
                @if ($spmbDibuka)
                    <a href="{{ route('spmb') }}" class="tombol-utama">
                        Info penerimaan murid baru
                        <x-heroicon-m-arrow-right class="h-4 w-4" aria-hidden="true" />
                    </a>
                    <a href="{{ route('profil') }}" class="tombol-kedua">Profil sekolah</a>
                @else
                    <a href="{{ route('profil') }}" class="tombol-utama">
                        Kenali sekolah kami
                        <x-heroicon-m-arrow-right class="h-4 w-4" aria-hidden="true" />
                    </a>
                    <a href="{{ route('kontak') }}" class="tombol-kedua">Hubungi sekolah</a>
                @endif
            </div>
        </div>

        @if ($foto)
            <img src="{{ $foto }}" alt="{{ Pengaturan::ambil('beranda_keterangan_foto') ?: 'Foto '.Pengaturan::ambil('nama_singkat') }}"
                 class="aspect-[4/3] w-full rounded-xl border border-garis object-cover">
        @else
            {{-- Tanpa foto pembuka: kartu identitas sekolah --}}
            <div class="kartu p-6 sm:p-8">
                <div class="flex items-center gap-4">
                    @if ($logo = Pengaturan::urlBerkas('logo_sekolah'))
                        <img src="{{ $logo }}" alt="" class="h-16 w-16 object-contain">
                    @endif
                    <div>
                        <p class="text-lg font-extrabold text-slate-900">{{ Pengaturan::ambil('nama_sekolah') }}</p>
                        <p class="teks-bantu">{{ Pengaturan::ambil('status_sekolah') }} · {{ Pengaturan::ambil('kota') }}</p>
                    </div>
                </div>
                <dl class="mt-6 grid grid-cols-2 gap-x-6 gap-y-4 border-t border-garis pt-6 text-sm">
                    @foreach (array_filter(['NPSN' => Pengaturan::ambil('npsn'), 'Akreditasi' => Pengaturan::ambil('akreditasi'), 'Kurikulum' => Pengaturan::ambil('kurikulum'), 'Jam layanan' => Pengaturan::ambil('jam_layanan')], 'filled') as $label => $nilai)
                        <div>
                            <dt class="text-slate-600">{{ $label }}</dt>
                            <dd class="font-bold text-slate-900">{{ $nilai }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        @endif
    </section>

    @if (count($angka) >= 2)
        <section aria-label="Sekolah dalam angka" class="wadah">
            <dl @class(["kartu grid grid-cols-2 divide-garis sm:divide-x", ["sm:grid-cols-2", "sm:grid-cols-3", "sm:grid-cols-4"][count($angka) - 2]])>
                @foreach ($angka as $label => $nilai)
                    <div class="px-5 py-5 text-center">
                        <dd class="text-3xl font-extrabold text-slate-900">{{ $nilai }}</dd>
                        <dt class="mt-1 text-sm text-slate-600">{{ $label }}</dt>
                    </div>
                @endforeach
            </dl>
        </section>
    @endif

    <section class="wadah mt-14">
        <div class="flex items-end justify-between gap-4">
            <h2 class="judul-bagian">Kabar sekolah</h2>
            <a href="{{ route('berita') }}" class="tautan text-sm">Semua berita</a>
        </div>
        @if ($berita->isEmpty())
            <x-kosong judul="Belum ada berita yang terbit" class="mt-5" :tautan="route('agenda')" label-tautan="Lihat agenda sekolah">
                Kabar kegiatan sekolah akan tampil di sini. Sementara itu, lihat agenda kegiatan yang akan datang.
            </x-kosong>
        @else
            <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($berita as $item)
                    <x-kartu-berita :berita="$item" />
                @endforeach
            </div>
        @endif
    </section>

    @if ($tampilSambutan || $tampilAgenda)
        <section @class(['wadah mt-14 grid gap-6', 'lg:grid-cols-5' => $tampilSambutan && $tampilAgenda])>
            @if ($tampilSambutan)
                <div class="kartu flex flex-col gap-5 p-6 sm:flex-row lg:col-span-3">
                    @if ($fotoKepsek = Pengaturan::urlBerkas('kepsek_foto'))
                        <img src="{{ $fotoKepsek }}" alt="Foto {{ $kepsek }}" class="aspect-[3/4] w-32 shrink-0 rounded-lg object-cover">
                    @endif
                    <div>
                        <h2 class="judul-bagian">Sambutan kepala sekolah</h2>
                        <p class="mt-3 text-slate-700">{{ $sambutan }}</p>
                        <p class="mt-4 font-extrabold text-slate-900">{{ $kepsek }}</p>
                        <p class="teks-bantu">Kepala {{ Pengaturan::ambil('nama_sekolah') }}</p>
                        @if (filled(Pengaturan::ambil('kepsek_sambutan')))
                            <a href="{{ route('profil') }}#sambutan" class="tautan mt-3 inline-block text-sm">Baca sambutan lengkap</a>
                        @endif
                    </div>
                </div>
            @endif

            @if ($tampilAgenda)
                <div class="kartu p-6 lg:col-span-2">
                    <div class="flex items-end justify-between gap-4">
                        <h2 class="judul-bagian">Agenda terdekat</h2>
                        <a href="{{ route('agenda') }}" class="tautan text-sm">Semua agenda</a>
                    </div>
                    @if ($agenda->isEmpty())
                        <p class="mt-4 teks-bantu">Belum ada agenda yang akan datang. Jadwal kegiatan dan hari libur akan tampil di sini setelah diumumkan sekolah.</p>
                    @else
                        <ul class="mt-4 divide-y divide-garis">
                            @foreach ($agenda as $item)
                                <li class="flex gap-4 py-3">
                                    <span class="w-14 shrink-0 rounded-lg border border-garis py-1.5 text-center leading-tight">
                                        <span class="block text-xl font-extrabold text-slate-900">{{ $item->mulai->format('j') }}</span>
                                        <span class="block text-xs font-bold text-slate-600">{{ $item->mulai->translatedFormat('M') }}</span>
                                    </span>
                                    <span>
                                        <span class="block font-bold text-slate-900">{{ $item->judul }}</span>
                                        <span class="block text-sm text-slate-600">
                                            {{ $item->tanggalTeks() }}@if ($item->libur) · <span class="font-bold text-brand-800">Libur</span>@endif
                                        </span>
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif
        </section>
    @endif

    @if (Pengaturan::aktif('beranda_tampil_program'))
        <section class="wadah mt-14">
            <div class="kartu grid gap-8 overflow-hidden lg:grid-cols-2">
                <div class="p-6 sm:p-8">
                    <p class="font-bold text-brand-800">Program unggulan</p>
                    <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">TEBAK AKSI</h2>
                    <p class="mt-1 font-semibold text-slate-600">{{ Pengaturan::ambil('tebakaksi_kepanjangan') }}</p>
                    <p class="mt-4 text-slate-700">{{ Pengaturan::ambil('tebakaksi_ringkasan') }}</p>
                    @if ($poin = Pengaturan::daftar('tebakaksi_poin'))
                        <ol class="mt-5 space-y-3">
                            @foreach ($poin as $i => $item)
                                <li class="flex gap-3">
                                    <span class="grid h-7 w-7 shrink-0 place-items-center rounded-md bg-brand-50 text-sm font-extrabold text-brand-800">{{ $i + 1 }}</span>
                                    <span><b class="text-slate-900">{{ $item['judul'] }}.</b> <span class="text-slate-600">{{ $item['keterangan'] }}</span></span>
                                </li>
                            @endforeach
                        </ol>
                    @endif
                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="{{ route('tebakaksi') }}" class="tombol-utama">
                            Tentang program
                            <x-heroicon-m-arrow-right class="h-4 w-4" aria-hidden="true" />
                        </a>
                        <a href="{{ Pengaturan::ambil('tebakaksi_url') }}" class="tombol-kedua" target="_blank" rel="noopener">Buka aplikasi TEBAK AKSI</a>
                    </div>
                </div>
                @if ($gambarProgram = Pengaturan::urlBerkas('tebakaksi_gambar'))
                    <img src="{{ $gambarProgram }}" alt="Kegiatan Teras Baca" loading="lazy" class="h-full min-h-64 w-full object-cover">
                @endif
            </div>
        </section>
    @endif

    @if ($prestasi->isNotEmpty())
        <section class="wadah mt-14">
            <div class="flex items-end justify-between gap-4">
                <h2 class="judul-bagian">Prestasi terbaru</h2>
                <a href="{{ route('prestasi') }}" class="tautan text-sm">Semua prestasi</a>
            </div>
            <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($prestasi as $item)
                    <x-kartu-prestasi :prestasi="$item" />
                @endforeach
            </div>
        </section>
    @endif

    @if ($album->isNotEmpty())
        <section class="wadah mt-14">
            <div class="flex items-end justify-between gap-4">
                <h2 class="judul-bagian">Galeri kegiatan</h2>
                <a href="{{ route('galeri') }}" class="tautan text-sm">Semua album</a>
            </div>
            <div class="mt-5 grid grid-cols-2 gap-4 lg:grid-cols-4">
                @foreach ($album as $item)
                    <x-kartu-album :album="$item" />
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.publik>
