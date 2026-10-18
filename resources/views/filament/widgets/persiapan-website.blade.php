<x-filament-widgets::widget>
    <x-filament::section heading="Lengkapi website" description="Bagian berikut masih kosong, sehingga belum tampil di website. Daftar ini hilang sendiri bila semuanya sudah diisi.">
        <ul style="display:grid;gap:.5rem">
            @foreach (static::belumLengkap() as $item)
                <li style="display:flex;align-items:center;justify-content:space-between;gap:1rem;border-bottom:1px solid rgb(0 0 0 / .06);padding-bottom:.5rem">
                    <span>{{ $item['teks'] }}</span>
                    <x-filament::link :href="$item['url']" weight="bold">Isi sekarang</x-filament::link>
                </li>
            @endforeach
        </ul>
    </x-filament::section>
</x-filament-widgets::widget>
