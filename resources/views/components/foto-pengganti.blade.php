@props(['teks' => null])

{{-- Pengganti foto untuk berita atau prestasi tanpa gambar: logo sekolah di atas warna lembut, bukan foto stok --}}
@php $logo = \App\Models\Pengaturan::urlBerkas('logo_sekolah'); @endphp
<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center gap-2 bg-brand-50 text-brand-800']) }} aria-hidden="true">
    @if ($logo)
        <img src="{{ $logo }}" alt="" class="h-1/3 max-h-20 min-h-8 w-auto object-contain opacity-80">
    @else
        <svg viewBox="0 0 40 40" class="h-1/3 max-h-16 min-h-8 w-auto opacity-80">
            <path d="M8 12.5c3.8-1.3 8-1 12 1.4v16c-4-2.4-8.2-2.7-12-1.4z" style="fill: var(--aksen-700)" />
            <path d="M32 12.5c-3.8-1.3-8-1-12 1.4v16c4-2.4 8.2-2.7 12-1.4z" style="fill: var(--aksen-300)" />
        </svg>
    @endif
    @if ($teks)
        <span class="text-xs font-bold">{{ $teks }}</span>
    @endif
</div>
