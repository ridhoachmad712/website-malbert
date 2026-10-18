{{-- Pratinjau palet di panel admin; memakai gaya langsung karena CSS website tidak dimuat di panel --}}
@php $a = $palet['aksen']; @endphp
<div style="display:flex;flex-direction:column;gap:.75rem;padding:1rem;border:1px solid #e5e7eb;border-radius:.75rem;background:#f5f6f8;max-width:34rem">
    <div style="display:flex;align-items:center;gap:.6rem">
        <svg viewBox="0 0 40 40" style="height:2rem;width:2rem" aria-hidden="true"><rect width="40" height="40" rx="9" fill="{{ $a['700'] }}"/><path d="M8 12.5c3.8-1.3 8-1 12 1.4v16c-4-2.4-8.2-2.7-12-1.4z" fill="#fff"/><path d="M32 12.5c-3.8-1.3-8-1-12 1.4v16c4-2.4 8.2-2.7 12-1.4z" fill="{{ $a['100'] }}"/></svg>
        <span style="font-weight:800;color:#1e293b">{{ \App\Models\Pengaturan::ambil('nama_singkat') }}</span>
        <span style="margin-left:auto;font-size:.8rem;font-weight:700;color:{{ $a['800'] }};border-bottom:2px solid {{ $a['700'] }};padding-bottom:.15rem">Menu aktif</span>
    </div>
    <div style="font-size:1.3rem;font-weight:800;color:#0f172a">Judul halaman</div>
    <div style="display:flex;flex-wrap:wrap;gap:.75rem;align-items:center">
        <span style="background:{{ $a['700'] }};color:#fff;font-weight:700;font-size:.85rem;padding:.55rem 1rem;border-radius:.5rem">Tombol utama</span>
        <span style="color:{{ $a['700'] }};font-weight:700;font-size:.85rem;text-decoration:underline">Tautan</span>
        <span style="background:{{ $a['50'] }};color:{{ $a['800'] }};font-weight:700;font-size:.75rem;padding:.2rem .5rem;border-radius:.375rem">Pengumuman</span>
    </div>
</div>
