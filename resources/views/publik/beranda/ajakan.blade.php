@use('App\Models\Pengaturan')

<div class="wadah">
    <div class="kartu flex flex-col items-start gap-6 p-8 sm:p-10 lg:flex-row lg:items-center lg:justify-between">
        <div class="max-w-2xl">
            <h2 id="{{ $bagian['id'] }}" class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">{{ Pengaturan::ambil('ajakan_judul') }}</h2>
            @if ($teks = Pengaturan::ambil('ajakan_teks'))
                <p class="mt-3 text-lg text-slate-600">{{ $teks }}</p>
            @endif
        </div>
        <div class="flex shrink-0 flex-wrap gap-3">
            <x-tombol-tautan :tautan="Pengaturan::tautan('ajakan_tombol1_')" panah />
            <x-tombol-tautan :tautan="Pengaturan::tautan('ajakan_tombol2_')" gaya="kedua" />
        </div>
    </div>
</div>
