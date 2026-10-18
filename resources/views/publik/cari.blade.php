<x-layouts.publik judul="Cari" deskripsi="Cari berita, pengumuman, halaman, dan dokumen sekolah.">
    <x-slot:kepala>
        <meta name="robots" content="noindex">
    </x-slot:kepala>

    <x-kepala-halaman judul="Cari di website" :jejak="['Cari' => route('cari')]">
        <form method="get" action="{{ route('cari') }}" role="search" class="mt-6 flex max-w-2xl gap-2">
            <label for="kata-cari" class="sr-only">Kata yang dicari</label>
            <input type="search" id="kata-cari" name="q" value="{{ $kata }}" minlength="3" maxlength="100" required
                   placeholder="Contoh: libur semester, pramuka, rapor" class="isian" autofocus>
            <button type="submit" class="tombol-utama shrink-0">
                <x-heroicon-m-magnifying-glass class="h-4 w-4" aria-hidden="true" /> Cari
            </button>
        </form>
    </x-kepala-halaman>

    <div class="wadah mt-10 max-w-4xl">
        @if ($kata === '')
            <p class="teks-bantu">Ketik paling sedikit 3 huruf, lalu tekan Cari.</p>
        @elseif (mb_strlen($kata) < 3)
            <p class="teks-bantu">Kata yang dicari terlalu pendek. Ketik paling sedikit 3 huruf.</p>
        @elseif ($hasil->isEmpty())
            <x-kosong :judul="'Tidak ada hasil untuk “'.$kata.'”'" :tautan="route('kontak')" label-tautan="Tanyakan ke sekolah">
                Coba kata lain yang lebih umum, atau periksa ejaannya. Bila informasi yang dicari belum ada, silakan tanyakan ke sekolah.
            </x-kosong>
        @else
            <p class="teks-bantu" role="status">{{ $hasil->count() }} hasil untuk “{{ $kata }}”</p>
            <ul class="mt-4 kartu divide-y divide-garis">
                @foreach ($hasil as $item)
                    <li>
                        <a href="{{ $item['url'] }}" class="group block p-5 hover:bg-abu">
                            <span class="lencana">{{ $item['jenis'] }}</span>
                            <span class="mt-2 block font-extrabold text-slate-900 underline-offset-4 group-hover:text-brand-800 group-hover:underline">{{ $item['judul'] }}</span>
                            @if (filled($item['cuplikan']))
                                <span class="mt-1 block text-sm text-slate-600">{{ \Illuminate\Support\Str::limit($item['cuplikan'], 180) }}</span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</x-layouts.publik>
