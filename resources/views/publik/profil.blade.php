@use('App\Models\Pengaturan')

@php
    $identitas = array_filter([
        'Nama sekolah' => Pengaturan::ambil('nama_sekolah'),
        'NPSN' => Pengaturan::ambil('npsn'),
        'Status' => Pengaturan::ambil('status_sekolah'),
        'Akreditasi' => Pengaturan::ambil('akreditasi'),
        'Kurikulum' => Pengaturan::ambil('kurikulum'),
        'Tahun berdiri' => Pengaturan::ambil('tahun_berdiri'),
        'Rombongan belajar' => Pengaturan::ambil('jumlah_rombel'),
        'Jumlah murid' => Pengaturan::ambil('jumlah_murid'),
        'Guru dan tenaga kependidikan' => $jumlahGuru ? $jumlahGuru.' orang' : null,
        'Alamat' => Pengaturan::alamatLengkap(),
    ], 'filled');
    $kepsek = Pengaturan::ambil('kepsek_nama');
    $sambutan = Pengaturan::ambil('kepsek_sambutan');
@endphp

<x-layouts.publik judul="Profil Sekolah" :deskripsi="'Identitas, sambutan kepala sekolah, dan profil '.Pengaturan::ambil('nama_sekolah').'.'">
    <x-kepala-halaman judul="Profil sekolah" :jejak="['Profil' => route('profil')]" />

    <div class="wadah mt-10 grid gap-8 lg:grid-cols-3">
        <div class="space-y-8 lg:col-span-2">
            <section class="kartu p-6 sm:p-8">
                <h2 class="judul-bagian">Identitas sekolah</h2>
                <dl class="mt-5 divide-y divide-garis">
                    @foreach ($identitas as $label => $nilai)
                        <div class="grid gap-1 py-3 sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm text-slate-600">{{ $label }}</dt>
                            <dd class="font-bold text-slate-900 sm:col-span-2">{{ $nilai }}</dd>
                        </div>
                    @endforeach
                </dl>
                @if ($resmi = Pengaturan::ambil('tautan_data_resmi'))
                    <a href="{{ $resmi }}" target="_blank" rel="noopener" class="tautan mt-4 inline-block text-sm">Lihat data resmi sekolah</a>
                @endif
            </section>

            <section id="sambutan" class="kartu scroll-mt-24 p-6 sm:p-8">
                <h2 class="judul-bagian">Sambutan kepala sekolah</h2>
                @if (filled($sambutan) || filled($kepsek))
                    <div class="mt-5 flex flex-col gap-6 sm:flex-row">
                        @if ($foto = Pengaturan::urlBerkas('kepsek_foto'))
                            <img src="{{ $foto }}" alt="Foto {{ $kepsek }}" class="aspect-[3/4] w-40 shrink-0 rounded-lg object-cover">
                        @endif
                        <div class="min-w-0">
                            @if (filled($sambutan))
                                <x-isi :html="$sambutan" />
                            @else
                                <p class="text-slate-700">{{ Pengaturan::ambil('kepsek_sambutan_singkat') }}</p>
                            @endif
                            @if ($kepsek)
                                <p class="mt-5 font-extrabold text-slate-900">{{ $kepsek }}</p>
                                @if ($nip = Pengaturan::ambil('kepsek_nip'))
                                    <p class="teks-bantu">NIP {{ $nip }}</p>
                                @endif
                            @endif
                        </div>
                    </div>
                @else
                    <p class="mt-3 teks-bantu">Sambutan kepala sekolah belum tersedia. Untuk informasi sekolah, silakan menghubungi kami melalui halaman Kontak.</p>
                @endif
            </section>
        </div>

        <aside class="space-y-4">
            <h2 class="font-extrabold text-slate-900">Tentang sekolah</h2>
            @foreach ($halaman as $item)
                <a href="{{ route('halaman', $item->slug) }}" class="kartu group block p-4 hover:border-slate-300">
                    <span class="font-bold text-slate-900 group-hover:text-brand-800 group-hover:underline underline-offset-4">{{ $item->judul }}</span>
                    @if ($item->ringkasan)
                        <span class="mt-1 block text-sm text-slate-600">{{ $item->ringkasan }}</span>
                    @endif
                </a>
            @endforeach
            <a href="{{ route('guru') }}" class="kartu group block p-4 hover:border-slate-300">
                <span class="font-bold text-slate-900 group-hover:text-brand-800 group-hover:underline underline-offset-4">Guru dan tenaga kependidikan</span>
                <span class="mt-1 block text-sm text-slate-600">Kenali para pendidik di sekolah kami.</span>
            </a>
            <a href="{{ route('tebakaksi') }}" class="kartu group block p-4 hover:border-slate-300">
                <span class="font-bold text-slate-900 group-hover:text-brand-800 group-hover:underline underline-offset-4">Program unggulan TEBAK AKSI</span>
                <span class="mt-1 block text-sm text-slate-600">{{ Pengaturan::ambil('tebakaksi_kepanjangan') }}</span>
            </a>
        </aside>
    </div>
</x-layouts.publik>
