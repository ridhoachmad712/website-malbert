@props(['html'])

{{-- Isi dari editor admin, selalu dibersihkan sebelum ditampilkan --}}
<div {{ $attributes->merge(['class' => 'isi-konten']) }}>{!! \Illuminate\Support\Str::sanitizeHtml((string) $html) !!}</div>
