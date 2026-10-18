{{-- Penampil foto besar untuk tautan beratribut data-penampil (skripnya di resources/js/app.js) --}}
<dialog class="penampil" aria-label="Penampil foto">
    <div class="flex h-full flex-col">
        <div class="flex items-center justify-between gap-4 px-4 py-3 text-white">
            <p data-nomor class="text-sm font-bold"></p>
            <button type="button" data-tutup class="tombol min-h-11 border border-white/30 text-white hover:bg-white/10">
                <x-heroicon-o-x-mark class="h-5 w-5" aria-hidden="true" /> Tutup
            </button>
        </div>
        <div class="relative flex min-h-0 flex-1 items-center justify-center px-4">
            <img alt="" class="max-h-full max-w-full rounded-lg object-contain">
        </div>
        <div class="flex items-center justify-between gap-4 px-4 py-3 text-white">
            <button type="button" data-sebelumnya class="tombol min-h-11 border border-white/30 text-white hover:bg-white/10">
                <x-heroicon-o-chevron-left class="h-5 w-5" aria-hidden="true" /> Sebelumnya
            </button>
            <p data-keterangan class="hidden text-center text-sm sm:block"></p>
            <button type="button" data-berikutnya class="tombol min-h-11 border border-white/30 text-white hover:bg-white/10">
                Berikutnya <x-heroicon-o-chevron-right class="h-5 w-5" aria-hidden="true" />
            </button>
        </div>
    </div>
</dialog>
