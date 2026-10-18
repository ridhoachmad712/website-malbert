@use('App\Models\Pengaturan')

<x-layouts.publik judul="Alumni" :deskripsi="Pengaturan::ambil('alumni_pengantar')">
    <x-kepala-halaman judul="Alumni" :ringkasan="Pengaturan::ambil('alumni_pengantar')"
        :jejak="['Profil' => route('profil'), 'Alumni' => route('alumni')]" />

    <div class="wadah mt-10 space-y-12">
        @if ($alumni->isEmpty())
            <x-kosong judul="Belum ada cerita alumni yang ditampilkan">
                Cerita alumni akan tampil di sini setelah diperiksa admin.
                @if (Pengaturan::aktif('alumni_formulir_tampil')) Bila Anda alumni sekolah ini, kirim kabar Anda melalui formulir di bawah. @endif
            </x-kosong>
        @else
            <ul class="grid gap-6 md:grid-cols-2">
                @foreach ($alumni as $item)
                    <li class="kartu flex gap-5 p-5 sm:p-6">
                        @if ($foto = $item->urlKecil('foto'))
                            <img src="{{ $foto }}" alt="Foto {{ $item->nama }}" loading="lazy" class="h-20 w-20 shrink-0 rounded-full object-cover sm:h-24 sm:w-24">
                        @else
                            <span class="grid h-20 w-20 shrink-0 place-items-center rounded-full bg-brand-50 text-2xl font-extrabold text-brand-800 sm:h-24 sm:w-24" aria-hidden="true">{{ $item->inisial() }}</span>
                        @endif
                        <div class="min-w-0">
                            <p class="text-lg font-extrabold text-slate-900">{{ $item->nama }}</p>
                            <p class="text-sm text-slate-600">{{ collect([$item->tahun_lulus ? 'Lulus '.$item->tahun_lulus : null, $item->kegiatan])->filter()->implode(' · ') }}</p>
                            @if ($item->cerita)
                                <blockquote class="mt-3 border-l-4 border-brand-300 pl-4 text-slate-700">{!! nl2br(e($item->cerita)) !!}</blockquote>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

        @if (Pengaturan::aktif('alumni_formulir_tampil'))
            <section id="kirim" class="kartu scroll-mt-24 p-6 sm:p-8" aria-labelledby="judul-formulir-alumni">
                <h2 id="judul-formulir-alumni" class="judul-bagian">Kirim kabar alumni</h2>
                <p class="mt-2 max-w-2xl teks-bantu">Kiriman Anda diperiksa admin terlebih dahulu. Kontak hanya dipakai sekolah untuk menghubungi Anda dan tidak pernah ditampilkan.</p>

                @if (session('terkirim'))
                    <div class="mt-5 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-emerald-900" role="status">
                        <p class="font-bold">Terima kasih, kiriman Anda sudah kami terima.</p>
                        <p class="mt-1 text-sm">Admin akan memeriksanya sebelum ditampilkan.</p>
                    </div>
                @endif

                <form method="post" action="{{ route('alumni.kirim') }}#kirim" class="mt-6 space-y-5">
                    @csrf
                    <div class="hidden" aria-hidden="true">
                        <label for="situs">Jangan diisi</label>
                        <input type="text" id="situs" name="situs" tabindex="-1" autocomplete="off">
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="nama" class="label">Nama lengkap</label>
                            <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required maxlength="150" autocomplete="name" class="isian">
                            @error('nama') <p class="galat">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="tahun_lulus" class="label">Tahun lulus</label>
                            <input type="number" id="tahun_lulus" name="tahun_lulus" value="{{ old('tahun_lulus') }}" required min="1950" max="{{ now()->year }}" inputmode="numeric" class="isian">
                            @error('tahun_lulus') <p class="galat">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        <label for="kegiatan" class="label">Kegiatan sekarang <span class="font-normal text-slate-500">(boleh kosong)</span></label>
                        <input type="text" id="kegiatan" name="kegiatan" value="{{ old('kegiatan') }}" maxlength="200" placeholder="Contoh: Siswa SMP, mahasiswa, atau pekerjaan" class="isian">
                    </div>
                    <div>
                        <label for="cerita" class="label">Cerita atau pesan untuk adik kelas <span class="font-normal text-slate-500">(boleh kosong)</span></label>
                        <textarea id="cerita" name="cerita" rows="4" maxlength="1500" class="isian">{{ old('cerita') }}</textarea>
                        @error('cerita') <p class="galat">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="kontak" class="label">Nomor HP atau surel <span class="font-normal text-slate-500">(tidak ditampilkan)</span></label>
                        <input type="text" id="kontak" name="kontak" value="{{ old('kontak') }}" maxlength="120" class="isian">
                    </div>
                    <label class="flex items-start gap-3 text-sm text-slate-700">
                        <input type="checkbox" name="setuju_tampil" value="1" @checked(old('setuju_tampil')) class="mt-0.5 h-5 w-5 shrink-0 accent-[var(--aksen-700)]">
                        Saya setuju nama, tahun lulus, kegiatan, dan cerita saya ditampilkan di website setelah diperiksa admin.
                    </label>
                    <button type="submit" class="tombol-utama">Kirim</button>
                </form>
            </section>
        @endif
    </div>
</x-layouts.publik>
