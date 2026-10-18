@props(['html'])

{{-- Isi dari editor admin, selalu dibersihkan sebelum ditampilkan (lihat App\Support\Teks::isiRapi) --}}
<div {{ $attributes->merge(['class' => 'isi-konten']) }}>{!! \App\Support\Teks::isiRapi($html) !!}</div>
