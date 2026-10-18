@props(['tautan', 'gaya' => 'utama', 'panah' => false])

{{-- Tombol yang tujuannya diatur admin; tidak tampil bila tulisan atau tujuannya kosong --}}
@php
    $url = \App\Support\Tautan::url($tautan);
    $kelas = ['utama' => 'tombol-utama', 'kedua' => 'tombol-kedua', 'terang' => 'tombol-terang'][$gaya];
@endphp
@if (filled($tautan['label'] ?? null) && $url)
    <a href="{{ $url }}" {!! \App\Support\Tautan::atribut($tautan) !!} {{ $attributes->merge(['class' => $kelas]) }}>
        {{ $tautan['label'] }}
        @if (($tautan['jenis'] ?? 'internal') === 'url')
            <x-heroicon-m-arrow-top-right-on-square class="h-4 w-4" aria-hidden="true" />
        @elseif ($panah)
            <x-heroicon-m-arrow-right class="h-4 w-4" aria-hidden="true" />
        @endif
    </a>
@endif
