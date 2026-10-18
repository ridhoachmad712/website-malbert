@props(['judul', 'tautan' => null, 'labelTautan' => null])

{{-- Keadaan kosong: selalu menyebut sebabnya dan langkah berikutnya (DESIGN.md) --}}
<div {{ $attributes->merge(['class' => 'kartu px-6 py-10 text-center']) }}>
    <p class="font-bold text-slate-800">{{ $judul }}</p>
    <p class="mx-auto mt-1 max-w-md teks-bantu">{{ $slot }}</p>
    @if ($tautan)
        <a href="{{ $tautan }}" class="tombol-kedua mt-4">{{ $labelTautan }}</a>
    @endif
</div>
