<x-layouts.publik judul="Halaman tidak ditemukan">
    <div class="wadah mt-16 max-w-xl text-center">
        <p class="font-bold text-brand-800">Galat 404</p>
        <h1 class="judul-halaman mt-2">Halaman tidak ditemukan</h1>
        <p class="mt-4 text-slate-600">Alamat yang dibuka mungkin salah ketik, atau halamannya sudah dipindahkan. Silakan mulai dari Beranda.</p>
        <div class="mt-6 flex flex-wrap justify-center gap-3">
            <a href="{{ route('beranda') }}" class="tombol-utama">Ke Beranda</a>
            <a href="{{ route('berita') }}" class="tombol-kedua">Lihat berita</a>
        </div>
    </div>
</x-layouts.publik>
