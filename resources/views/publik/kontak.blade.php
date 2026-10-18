@use('App\Models\Pengaturan')
@use('App\Models\Pesan')

@php
    $peta = Pengaturan::ambil('peta_sematan');
    $tautanPeta = Pengaturan::ambil('tautan_peta');
@endphp

<x-layouts.publik judul="Kontak" :deskripsi="'Alamat, telepon, dan peta lokasi '.Pengaturan::ambil('nama_sekolah').'.'">
    <x-kepala-halaman judul="Hubungi kami" :jejak="['Kontak' => route('kontak')]"
        ringkasan="Kunjungi sekolah pada jam layanan, atau kirim pesan melalui formulir di bawah." />

    <div class="wadah mt-10 grid gap-8 lg:grid-cols-5">
        <div class="space-y-6 lg:col-span-2">
            <section class="kartu p-6">
                <h2 class="font-extrabold text-slate-900">{{ Pengaturan::ambil('nama_sekolah') }}</h2>
                <dl class="mt-4 space-y-4 text-sm">
                    @if ($alamat = Pengaturan::alamatLengkap())
                        <div>
                            <dt class="font-bold text-slate-600">Alamat</dt>
                            <dd class="mt-0.5 text-slate-900">{{ $alamat }}</dd>
                        </div>
                    @endif
                    @if ($jam = Pengaturan::ambil('jam_layanan'))
                        <div>
                            <dt class="font-bold text-slate-600">Jam layanan</dt>
                            <dd class="mt-0.5 text-slate-900">{{ $jam }}</dd>
                        </div>
                    @endif
                    @if ($telepon = Pengaturan::ambil('telepon'))
                        <div>
                            <dt class="font-bold text-slate-600">Telepon</dt>
                            <dd class="mt-0.5"><a href="tel:{{ preg_replace('/[^\d+]/', '', $telepon) }}" class="tautan">{{ $telepon }}</a></dd>
                        </div>
                    @endif
                    @if ($wa = Pengaturan::tautanWhatsapp())
                        <div>
                            <dt class="font-bold text-slate-600">WhatsApp</dt>
                            <dd class="mt-0.5"><a href="{{ $wa }}" target="_blank" rel="noopener" class="tautan">{{ Pengaturan::ambil('whatsapp') }}</a></dd>
                        </div>
                    @endif
                    @if ($surel = Pengaturan::ambil('email'))
                        <div>
                            <dt class="font-bold text-slate-600">Surel</dt>
                            <dd class="mt-0.5"><a href="mailto:{{ $surel }}" class="tautan break-all">{{ $surel }}</a></dd>
                        </div>
                    @endif
                </dl>
                @if ($medsos = Pengaturan::mediaSosial())
                    <ul class="mt-5 flex flex-wrap gap-x-4 gap-y-1 border-t border-garis pt-4 text-sm">
                        @foreach ($medsos as $nama => $url)
                            <li><a href="{{ $url }}" target="_blank" rel="noopener" class="tautan">{{ $nama }}</a></li>
                        @endforeach
                    </ul>
                @endif
            </section>

            @if ($peta)
                <div class="overflow-hidden rounded-xl border border-garis bg-white">
                    <iframe src="{{ $peta }}" title="Peta lokasi {{ Pengaturan::ambil('nama_singkat') }}" class="aspect-[4/3] w-full" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                </div>
            @endif
            @if ($tautanPeta)
                <a href="{{ $tautanPeta }}" target="_blank" rel="noopener" class="tombol-kedua w-full">Buka di Google Maps</a>
            @endif
        </div>

        <section class="kartu p-6 sm:p-8 lg:col-span-3">
            <h2 class="judul-bagian">Kirim pesan</h2>
            @if (session('terkirim'))
                <div class="mt-5 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-emerald-900" role="status">
                    <p class="font-bold">Pesan Anda sudah kami terima.</p>
                    <p class="mt-1 text-sm">Admin sekolah akan membacanya pada hari kerja. Bila Anda meninggalkan kontak, kami akan membalas melalui kontak tersebut.</p>
                </div>
            @endif
            <p class="mt-2 teks-bantu">Nama dan kontak boleh dikosongkan. Untuk hal mendesak, hubungi sekolah langsung melalui telepon atau WhatsApp.</p>

            <form method="post" action="{{ route('kontak.kirim') }}" class="mt-6 space-y-5">
                @csrf
                <div class="hidden" aria-hidden="true">
                    <label for="situs">Jangan diisi</label>
                    <input type="text" id="situs" name="situs" tabindex="-1" autocomplete="off">
                </div>
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="nama" class="label">Nama <span class="font-normal text-slate-500">(boleh kosong)</span></label>
                        <input type="text" id="nama" name="nama" value="{{ old('nama') }}" maxlength="120" autocomplete="name" class="isian">
                        @error('nama') <p class="galat">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="kontak" class="label">Nomor HP atau surel <span class="font-normal text-slate-500">(boleh kosong)</span></label>
                        <input type="text" id="kontak" name="kontak" value="{{ old('kontak') }}" maxlength="120" class="isian">
                        @error('kontak') <p class="galat">{{ $message }}</p> @enderror
                    </div>
                </div>
                <fieldset>
                    <legend class="label">Jenis pesan</legend>
                    <div class="flex flex-wrap gap-2">
                        @foreach (Pesan::KATEGORI as $kunci => $label)
                            <label class="flex min-h-11 cursor-pointer items-center gap-2 rounded-lg border border-garis bg-white px-3 text-sm font-bold text-slate-700 has-[:checked]:border-brand-700 has-[:checked]:bg-brand-50 has-[:checked]:text-brand-800">
                                <input type="radio" name="kategori" value="{{ $kunci }}" @checked(old('kategori', 'pertanyaan') === $kunci) class="accent-[var(--aksen-700)]">
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    @error('kategori') <p class="galat">{{ $message }}</p> @enderror
                </fieldset>
                <div>
                    <label for="isi" class="label">Pesan</label>
                    <textarea id="isi" name="isi" rows="6" required minlength="10" maxlength="3000" class="isian">{{ old('isi') }}</textarea>
                    <p class="mt-1 text-sm text-slate-600">Mohon tidak menuliskan data pribadi anak, seperti NISN atau alamat rumah.</p>
                    @error('isi') <p class="galat">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="tombol-utama">Kirim pesan</button>
            </form>
        </section>
    </div>
</x-layouts.publik>
