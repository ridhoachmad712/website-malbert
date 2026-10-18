<x-layouts.publik :judul="$halaman->judul" :deskripsi="$halaman->ringkasan" :gambar="$halaman->urlBerkas('gambar')">
    <x-kepala-halaman :judul="$halaman->judul" :ringkasan="$halaman->ringkasan"
        :jejak="$halaman->grup === 'profil' ? ['Profil' => route('profil')] : []" />

    <div @class(['wadah mt-10 grid gap-8', 'lg:grid-cols-3' => $lainnya->count() > 1])>
        <article @class(['kartu p-6 sm:p-8', 'lg:col-span-2' => $lainnya->count() > 1])>
            @if (filled(strip_tags((string) $halaman->isi)))
                <x-isi :html="$halaman->isi" class="max-w-3xl" />
            @elseif (! $halaman->gambar)
                <p class="teks-bantu">Isi halaman ini sedang disiapkan. Silakan kembali lagi nanti, atau hubungi sekolah melalui halaman Kontak.</p>
            @endif

            @if ($gambar = $halaman->urlBerkas('gambar'))
                <a href="{{ $gambar }}" target="_blank" class="mt-6 block">
                    <img src="{{ $gambar }}" alt="{{ $halaman->judul }}" class="w-full rounded-lg border border-garis">
                    <span class="mt-2 block text-sm text-slate-600">Ketuk gambar untuk memperbesar.</span>
                </a>
            @endif
        </article>

        @if ($lainnya->count() > 1)
            <aside>
                <h2 class="font-extrabold text-slate-900">Profil lainnya</h2>
                <nav class="mt-3 kartu p-2" aria-label="Halaman profil">
                    <a href="{{ route('profil') }}" class="flex min-h-11 items-center rounded-lg px-3 text-sm font-bold text-slate-700 hover:bg-krem">Identitas dan sambutan</a>
                    @foreach ($lainnya as $item)
                        <a href="{{ route('halaman', $item->slug) }}" @if ($item->id === $halaman->id) aria-current="page" @endif
                           @class(['flex min-h-11 items-center rounded-lg px-3 text-sm font-bold',
                               'bg-brand-50 text-brand-800' => $item->id === $halaman->id,
                               'text-slate-700 hover:bg-krem' => $item->id !== $halaman->id])>{{ $item->judul }}</a>
                    @endforeach
                    <a href="{{ route('guru') }}" class="flex min-h-11 items-center rounded-lg px-3 text-sm font-bold text-slate-700 hover:bg-krem">Guru dan tenaga kependidikan</a>
                </nav>
            </aside>
        @endif
    </div>
</x-layouts.publik>
