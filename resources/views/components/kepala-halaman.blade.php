@props(['judul', 'jejak' => [], 'ringkasan' => null])

{{-- Judul halaman dengan jejak tautan (breadcrumb) --}}
<div class="border-b border-garis bg-white">
    <div class="wadah py-8 sm:py-10">
        @if ($jejak)
            <nav aria-label="Jejak halaman" class="mb-3 text-sm">
                <ol class="flex flex-wrap items-center gap-x-2 gap-y-1 text-slate-600">
                    <li><a href="{{ route('beranda') }}" class="hover:text-slate-900 hover:underline">Beranda</a></li>
                    @foreach ($jejak as $label => $url)
                        <li aria-hidden="true">/</li>
                        <li><a href="{{ $url }}" class="hover:text-slate-900 hover:underline">{{ $label }}</a></li>
                    @endforeach
                </ol>
            </nav>
        @endif
        <h1 class="judul-halaman max-w-4xl">{{ $judul }}</h1>
        @if ($ringkasan)
            <p class="mt-3 max-w-3xl text-lg text-slate-600">{{ $ringkasan }}</p>
        @endif
        {{ $slot }}
    </div>
</div>
