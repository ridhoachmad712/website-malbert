@use('App\Models\Pengaturan')

{{-- Logo sekolah dengan nama singkat; nama resmi tampil di layar lebar --}}
<span {{ $attributes->merge(['class' => 'flex min-w-0 items-center gap-2.5']) }}>
    @if ($logo = Pengaturan::urlBerkas('logo_sekolah'))
        <img src="{{ $logo }}" alt="Logo {{ Pengaturan::ambil('nama_singkat') }}" class="h-10 w-10 shrink-0 object-contain">
    @else
        <svg viewBox="0 0 40 40" class="h-9 w-9 shrink-0" aria-hidden="true">
            <rect width="40" height="40" rx="9" style="fill: var(--aksen-700)" />
            <path d="M8 12.5c3.8-1.3 8-1 12 1.4v16c-4-2.4-8.2-2.7-12-1.4z" fill="#fff" />
            <path d="M32 12.5c-3.8-1.3-8-1-12 1.4v16c4-2.4 8.2-2.7 12-1.4z" style="fill: var(--aksen-100)" />
        </svg>
    @endif
    <span class="min-w-0 leading-tight">
        <span class="block truncate text-base font-extrabold tracking-tight text-slate-800 sm:text-lg">{{ Pengaturan::ambil('nama_singkat') }}</span>
        <span class="hidden truncate text-xs font-semibold text-slate-600 sm:block">{{ Pengaturan::ambil('nama_sekolah') }}</span>
    </span>
</span>
