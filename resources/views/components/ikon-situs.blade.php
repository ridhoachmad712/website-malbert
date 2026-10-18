{{-- Warna palet pilihan admin dan ikon tab browser (logo sekolah, atau ikon bawaan dalam warna palet) --}}
<style>{!! \App\Support\PaletWarna::css() !!}</style>
@if ($logo = \App\Models\Pengaturan::urlBerkas('logo_sekolah'))
    <link rel="icon" href="{{ $logo }}">
@else
    <link rel="icon" type="image/svg+xml" href="{{ \App\Support\PaletWarna::ikonDataUri() }}">
@endif
