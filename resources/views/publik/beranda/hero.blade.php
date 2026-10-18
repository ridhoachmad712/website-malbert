@use('App\Models\Pengaturan')

@php
    $foto = Pengaturan::urlBerkas('beranda_foto');
    $fokus = ['top' => 'object-top', 'center' => 'object-center', 'bottom' => 'object-bottom'][Pengaturan::ambil('beranda_fokus')] ?? 'object-center';
    $angka = array_filter([
        'Akreditasi' => Pengaturan::ambil('akreditasi'),
        'Rombongan belajar' => Pengaturan::ambil('jumlah_rombel'),
        'Murid' => Pengaturan::ambil('jumlah_murid'),
        'Guru dan tendik' => $jumlahGuru ?: null,
    ], 'filled');
    $logo = Pengaturan::urlBerkas('logo_sekolah');
@endphp

{{-- Hero: foto selebar layar dengan lapisan gelap rata (bukan gradasi) agar tulisan putih selalu terbaca --}}
<section class="relative isolate overflow-hidden bg-slate-900 text-white">
    @if ($foto)
        <img src="{{ $foto }}" alt="{{ Pengaturan::ambil('beranda_keterangan_foto') ?: '' }}" fetchpriority="high"
             class="absolute inset-0 -z-10 h-full w-full object-cover {{ $fokus }}">
        <div class="absolute inset-0 -z-10 bg-slate-950/60" aria-hidden="true"></div>
    @endif

    <div @class(['wadah flex flex-col justify-center gap-10 py-16 sm:py-24 lg:flex-row lg:items-center lg:justify-between',
        'min-h-[34rem] lg:min-h-[78vh]' => $foto,
        'pb-24 sm:pb-28' => count($angka) >= 2])>
        <div class="max-w-2xl">
            <p class="font-bold text-white/85">{{ Pengaturan::ambil('nama_sekolah') }}</p>
            <h1 class="mt-3 text-4xl font-extrabold leading-tight tracking-tight sm:text-5xl lg:text-6xl">{{ Pengaturan::ambil('beranda_judul') }}</h1>
            <p class="mt-5 max-w-xl text-lg text-white/85 sm:text-xl">{{ Pengaturan::ambil('beranda_paragraf') }}</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <x-tombol-tautan :tautan="Pengaturan::tautan('beranda_tombol1_')" panah />
                <x-tombol-tautan :tautan="Pengaturan::tautan('beranda_tombol2_')" gaya="terang" />
            </div>
        </div>

        @if (! $foto && $logo)
            {{-- Tanpa foto hero: logo sekolah sebagai penanda --}}
            <img src="{{ $logo }}" alt="" class="hidden h-48 w-48 shrink-0 rounded-2xl bg-white p-5 object-contain lg:block">
        @endif
    </div>
</section>

@if (count($angka) >= 2)
    <div class="wadah relative z-10 -mt-14 sm:-mt-16">
        {{-- Satu-satunya panel berbayang tipis: kartu ini menumpuk di atas tepi foto hero (DESIGN.md) --}}
        <dl @class(['grid grid-cols-2 divide-garis rounded-xl border border-garis bg-white shadow-sm sm:divide-x', ['sm:grid-cols-2', 'sm:grid-cols-3', 'sm:grid-cols-4'][count($angka) - 2]])>
            @foreach ($angka as $label => $nilai)
                <div class="px-4 py-5 text-center sm:py-6">
                    <dd class="text-3xl font-extrabold text-slate-900 sm:text-4xl">{{ $nilai }}</dd>
                    <dt class="mt-1 text-sm text-slate-600">{{ $label }}</dt>
                </div>
            @endforeach
        </dl>
    </div>
@endif
