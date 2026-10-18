{{-- Carousel guru: digeser manual (jari, tombol, atau papan ketik), tanpa putar otomatis --}}
<div class="wadah" data-carousel>
    <x-kepala-bagian :bagian="$bagian" :tautan="route('guru')" label-tautan="Lihat semua">
        <div class="hidden gap-2 sm:flex">
            <button type="button" data-geser="-1" class="tombol-kedua px-3" aria-label="Geser ke sebelumnya" aria-controls="lintasan-guru">
                <x-heroicon-m-chevron-left class="h-5 w-5" aria-hidden="true" />
            </button>
            <button type="button" data-geser="1" class="tombol-kedua px-3" aria-label="Geser ke berikutnya" aria-controls="lintasan-guru">
                <x-heroicon-m-chevron-right class="h-5 w-5" aria-hidden="true" />
            </button>
        </div>
    </x-kepala-bagian>

    <ul id="lintasan-guru" data-lintasan tabindex="0" aria-label="Daftar guru dan tenaga kependidikan, geser untuk melihat lainnya"
        class="lintasan mt-8 -mx-4 flex snap-x snap-mandatory gap-4 overflow-x-auto scroll-smooth px-4 pb-3 sm:mx-0 sm:px-0">
        @foreach ($guru as $item)
            <li class="w-[62%] shrink-0 snap-start sm:w-[38%] md:w-[29%] lg:w-[calc((100%-4rem)/5)]">
                <a href="{{ $item->url() }}" class="kartu group block h-full p-3">
                    <x-foto-guru :guru="$item" />
                    <span class="mt-3 block font-extrabold leading-snug text-slate-900 underline-offset-4 group-hover:text-brand-800 group-hover:underline">{{ $item->nama }}</span>
                    @if ($item->jabatan)
                        <span class="mt-0.5 block text-sm text-slate-600">{{ $item->jabatan }}</span>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>
    <p class="mt-2 text-sm text-slate-600 sm:hidden">Geser untuk melihat guru lainnya.</p>
</div>
