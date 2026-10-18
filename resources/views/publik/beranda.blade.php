@use('App\Models\Pengaturan')

@php
    // Ritme latar: bagian terang bergantian krem dan putih; Program TEBAK AKSI selalu pita gelap
    $putih = false;
@endphp

<x-layouts.publik>
    <x-slot:kepala>
        @if ($foto = Pengaturan::urlBerkas('beranda_foto'))
            <link rel="preload" as="image" href="{{ $foto }}" fetchpriority="high">
        @endif
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

    @include('publik.beranda.hero')

    @foreach ($bagian as $kunci => $item)
        @php
            $gelap = $kunci === 'program';
            if (! $gelap && $kunci !== 'ajakan') {
                $putih = ! $putih;
            }
        @endphp
        <section aria-labelledby="bagian-{{ $kunci }}" @class([
            'py-14 sm:py-20' => $kunci !== 'ajakan',
            'pt-14 sm:pt-20' => $kunci === 'ajakan',
            'bg-slate-900 text-white' => $gelap,
            'border-y border-garis bg-white' => ! $gelap && $kunci !== 'ajakan' && ! $putih,
        ])>
            @include('publik.beranda.'.$kunci, ['bagian' => $item + ['id' => 'bagian-'.$kunci]])
        </section>
    @endforeach
</x-layouts.publik>
