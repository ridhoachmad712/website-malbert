@use('App\Models\Pengaturan')

@php
    $status = Pengaturan::ambil('spmb_status');
    $portal = Pengaturan::ambil('spmb_portal_url');
    $tahun = Pengaturan::ambil('spmb_tahun_ajaran');
    $jadwal = Pengaturan::daftar('spmb_jadwal');
    $jalur = Pengaturan::daftar('spmb_jalur');
    $alur = Pengaturan::daftar('spmb_alur');
    $tanya = Pengaturan::daftar('spmb_tanya');
    $syarat = Pengaturan::ambil('spmb_syarat');
    $kosong = ! $jadwal && ! $jalur && ! $alur && ! $tanya && blank(strip_tags((string) $syarat));
@endphp

<x-layouts.publik judul="Penerimaan Murid Baru" :deskripsi="Pengaturan::ambil('spmb_ringkasan')">
    <x-kepala-halaman :judul="'Penerimaan murid baru'.($tahun ? ' '.$tahun : '')" :ringkasan="Pengaturan::ambil('spmb_ringkasan')" :jejak="['SPMB' => route('spmb')]">
        <div class="mt-6 flex flex-wrap items-center gap-3">
            <span @class(['lencana text-sm', 'lencana-aksen' => $status === 'dibuka'])>{{ Pengaturan::STATUS_SPMB[$status] ?? '' }}</span>
            @if ($status === 'dibuka' && $portal)
                <a href="{{ $portal }}" target="_blank" rel="noopener" class="tombol-utama">
                    Daftar di portal SPMB
                    <x-heroicon-m-arrow-top-right-on-square class="h-4 w-4" aria-hidden="true" />
                </a>
            @endif
        </div>
        <p class="mt-4 max-w-3xl text-sm text-slate-600">
            Pendaftaran dilakukan melalui portal resmi Dinas Pendidikan Kota Makassar. Website sekolah ini tidak menerima atau menyimpan data pendaftar.
        </p>
    </x-kepala-halaman>

    <div class="wadah mt-10 grid gap-8 lg:grid-cols-3">
        <div class="space-y-8 lg:col-span-2">
            @if ($kosong)
                <x-kosong judul="Informasi penerimaan murid baru belum diumumkan" :tautan="route('kontak')" label-tautan="Hubungi sekolah">
                    Jadwal dan persyaratan akan diumumkan di halaman ini mengikuti ketentuan Dinas Pendidikan Kota Makassar.
                </x-kosong>
            @endif

            @if ($jadwal)
                <section class="kartu p-6 sm:p-8">
                    <h2 class="judul-bagian">Jadwal</h2>
                    <dl class="mt-4 divide-y divide-garis">
                        @foreach ($jadwal as $item)
                            <div class="grid gap-1 py-3 sm:grid-cols-2 sm:gap-4">
                                <dt class="font-bold text-slate-900">{{ $item['kegiatan'] }}</dt>
                                <dd class="text-slate-700">{{ $item['tanggal'] }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </section>
            @endif

            @if ($jalur)
                <section class="kartu p-6 sm:p-8">
                    <h2 class="judul-bagian">Jalur pendaftaran</h2>
                    <ul class="mt-4 grid gap-4 sm:grid-cols-2">
                        @foreach ($jalur as $item)
                            <li class="rounded-lg border border-garis p-4">
                                <p class="font-extrabold text-slate-900">{{ $item['nama'] }}</p>
                                @if (filled($item['kuota'] ?? null))
                                    <p class="mt-1 text-sm font-bold text-brand-800">Kuota {{ $item['kuota'] }}</p>
                                @endif
                                @if (filled($item['keterangan'] ?? null))
                                    <p class="mt-2 text-sm text-slate-600">{{ $item['keterangan'] }}</p>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

            @if (filled(strip_tags((string) $syarat)))
                <section class="kartu p-6 sm:p-8">
                    <h2 class="judul-bagian">Persyaratan</h2>
                    <x-isi :html="$syarat" class="mt-4" />
                </section>
            @endif

            @if ($alur)
                <section class="kartu p-6 sm:p-8">
                    <h2 class="judul-bagian">Langkah pendaftaran</h2>
                    <ol class="mt-5 space-y-5">
                        @foreach ($alur as $i => $item)
                            <li class="flex gap-4">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-brand-50 font-extrabold text-brand-800">{{ $i + 1 }}</span>
                                <div>
                                    <p class="font-extrabold text-slate-900">{{ $item['judul'] }}</p>
                                    @if (filled($item['keterangan'] ?? null))
                                        <p class="mt-1 text-slate-600">{{ $item['keterangan'] }}</p>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </section>
            @endif

            @if ($tanya)
                <section>
                    <h2 class="judul-bagian">Tanya jawab</h2>
                    <div class="mt-4 space-y-3">
                        @foreach ($tanya as $item)
                            <details class="kartu group">
                                <summary class="flex min-h-11 cursor-pointer items-center justify-between gap-4 px-5 py-4 font-bold text-slate-900">
                                    {{ $item['tanya'] }}
                                    <x-heroicon-m-chevron-down class="h-5 w-5 shrink-0 transition-transform group-open:rotate-180" aria-hidden="true" />
                                </summary>
                                <p class="border-t border-garis px-5 py-4 text-slate-700">{!! nl2br(e($item['jawab'])) !!}</p>
                            </details>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        <aside class="space-y-4">
            <div class="kartu p-6">
                <h2 class="font-extrabold text-slate-900">Butuh bantuan?</h2>
                @if ($narahubung = Pengaturan::ambil('spmb_narahubung'))
                    <p class="mt-2 text-sm text-slate-700">{!! nl2br(e($narahubung)) !!}</p>
                @else
                    <p class="mt-2 text-sm text-slate-600">Panitia penerimaan murid baru dapat dihubungi melalui kontak resmi sekolah.</p>
                @endif
                <div class="mt-4 flex flex-wrap gap-2">
                    @if ($wa = Pengaturan::tautanWhatsapp())
                        <a href="{{ $wa }}" target="_blank" rel="noopener" class="tombol-kedua">WhatsApp sekolah</a>
                    @endif
                    <a href="{{ route('kontak') }}" class="tombol-kedua">Kirim pertanyaan</a>
                </div>
            </div>
        </aside>
    </div>
</x-layouts.publik>
