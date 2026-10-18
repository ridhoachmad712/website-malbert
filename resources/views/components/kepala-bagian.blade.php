@props(['bagian', 'tautan' => null, 'labelTautan' => 'Lihat semua', 'gelap' => false])

{{-- Kepala setiap bagian Beranda: label kecil, judul, dan tautan "Lihat semua" (teks diatur admin) --}}
<div {{ $attributes->merge(['class' => 'flex flex-wrap items-end justify-between gap-4']) }}>
    <div>
        @if (filled($bagian['label']))
            <p @class(['text-sm font-bold', 'text-brand-800' => ! $gelap, 'text-brand-200' => $gelap])>{{ $bagian['label'] }}</p>
        @endif
        @if (filled($bagian['judul']))
            <h2 @if (isset($bagian['id'])) id="{{ $bagian['id'] }}" @endif @class(['mt-1 text-2xl font-extrabold tracking-tight sm:text-3xl', 'text-slate-900' => ! $gelap, 'text-white' => $gelap])>{{ $bagian['judul'] }}</h2>
        @endif
    </div>
    @if ($tautan || $slot->isNotEmpty())
        <div class="flex items-center gap-3">
            {{ $slot }}
            @if ($tautan)
                <a href="{{ $tautan }}" @class(['inline-flex min-h-11 items-center gap-1 text-sm font-bold underline-offset-4 hover:underline', 'text-brand-700' => ! $gelap, 'text-white' => $gelap])>
                    {{ $labelTautan }}
                    <x-heroicon-m-arrow-right class="h-4 w-4" aria-hidden="true" />
                </a>
            @endif
        </div>
    @endif
</div>
