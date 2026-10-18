@use('App\Models\DokumenAnggaran')
@use('App\Models\Pengaturan')

@php
    // Ringkasan tahun: dari laporan realisasi tahunan bila ada, kalau tidak dari jumlah laporan tahap 1 dan 2
    $tahunan = $dokumen->firstWhere('jenis', 'realisasi_tahunan');
    $tahap = $dokumen->whereIn('jenis', ['realisasi_tahap1', 'realisasi_tahap2'])->filter(fn ($item) => $item->penerimaan !== null || $item->belanja !== null);
    $ringkasan = $tahunan && ($tahunan->penerimaan !== null || $tahunan->belanja !== null)
        ? ['penerimaan' => $tahunan->penerimaan, 'belanja' => $tahunan->belanja, 'sumber' => 'laporan realisasi tahunan']
        : ($tahap->isNotEmpty() ? ['penerimaan' => $tahap->sum('penerimaan') ?: null, 'belanja' => $tahap->sum('belanja') ?: null, 'sumber' => 'jumlah laporan realisasi per tahap'] : null);
    $rkas = $dokumen->firstWhere('jenis', 'rkas');
@endphp

<x-layouts.publik judul="Transparansi Anggaran" deskripsi="Rencana kegiatan dan anggaran sekolah serta laporan realisasi penggunaan dana BOSP.">
    <x-kepala-halaman judul="Transparansi anggaran" :jejak="['Transparansi anggaran' => route('transparansi')]">
        <p class="mt-3 max-w-3xl text-base text-slate-600 sm:text-lg">{{ Pengaturan::ambil('transparansi_pengantar') }}</p>
        @if ($daftarTahun->count() > 1)
            <nav class="-mx-4 mt-6 tanpa-bilah flex gap-2 overflow-x-auto px-4 pb-1 sm:mx-0 sm:flex-wrap sm:overflow-visible sm:px-0" aria-label="Tahun anggaran">
                @foreach ($daftarTahun as $item)
                    <a href="{{ route('transparansi', ['tahun' => $item]) }}" @class(['tab', 'tab-aktif' => $item === $tahun, 'tab-pasif' => $item !== $tahun])
                       @if ($item === $tahun) aria-current="page" @endif>{{ $item }}</a>
                @endforeach
            </nav>
        @endif
    </x-kepala-halaman>

    <div class="wadah mt-10 space-y-8">
        @if ($dokumen->isEmpty())
            <x-kosong judul="Dokumen anggaran belum diunggah" :tautan="route('kontak')" label-tautan="Hubungi sekolah">
                Rencana kegiatan dan anggaran serta laporan realisasi penggunaan dana akan dipublikasikan di halaman ini. Untuk informasi lebih lanjut, silakan hubungi sekolah.
            </x-kosong>
        @else
            @if ($ringkasan || $rkas?->penerimaan)
                <section aria-labelledby="ringkasan-anggaran">
                    <h2 id="ringkasan-anggaran" class="judul-bagian">Ringkasan tahun {{ $tahun }}</h2>
                    <dl class="mt-4 grid gap-3 sm:grid-cols-3 sm:gap-4">
                        @if ($rkas?->penerimaan)
                            <div class="kartu px-4 py-3 sm:p-5">
                                <dt class="text-sm text-slate-600">Anggaran direncanakan (RKAS)</dt>
                                <dd class="mt-0.5 text-xl font-extrabold text-slate-900 sm:mt-1 sm:text-2xl">{{ DokumenAnggaran::rupiah($rkas->penerimaan) }}</dd>
                            </div>
                        @endif
                        @if ($ringkasan && $ringkasan['penerimaan'] !== null)
                            <div class="kartu px-4 py-3 sm:p-5">
                                <dt class="text-sm text-slate-600">Dana diterima</dt>
                                <dd class="mt-0.5 text-xl font-extrabold text-slate-900 sm:mt-1 sm:text-2xl">{{ DokumenAnggaran::rupiah($ringkasan['penerimaan']) }}</dd>
                            </div>
                        @endif
                        @if ($ringkasan && $ringkasan['belanja'] !== null)
                            <div class="kartu px-4 py-3 sm:p-5">
                                <dt class="text-sm text-slate-600">Dana digunakan</dt>
                                <dd class="mt-0.5 text-xl font-extrabold text-slate-900 sm:mt-1 sm:text-2xl">{{ DokumenAnggaran::rupiah($ringkasan['belanja']) }}</dd>
                            </div>
                        @endif
                    </dl>
                    @if ($ringkasan)
                        <p class="mt-2 text-sm text-slate-600">Angka diambil dari {{ $ringkasan['sumber'] }} yang diunggah sekolah. Rincian lengkap ada di dokumen di bawah.</p>
                    @endif
                </section>
            @endif

            <section aria-labelledby="dokumen-anggaran">
                <h2 id="dokumen-anggaran" class="judul-bagian">Dokumen tahun {{ $tahun }}</h2>
                <ul class="mt-4 kartu divide-y divide-garis">
                    @foreach ($dokumen as $item)
                        <li class="flex flex-col gap-4 p-5 md:flex-row md:items-center md:justify-between">
                            <div class="min-w-0">
                                <p class="flex flex-wrap gap-2">
                                    <span class="lencana-aksen">{{ $item->namaJenis() }}</span>
                                    <span class="lencana">{{ $item->namaSumber() }}</span>
                                </p>
                                <p class="mt-2 font-extrabold leading-snug text-slate-900">{{ $item->judul }}</p>
                                <p class="mt-1 text-sm text-slate-600">
                                    {{ collect([$item->periode, $item->tanggal?->translatedFormat('j F Y'), strtoupper(pathinfo($item->berkas, PATHINFO_EXTENSION)), $item->ukuranTeks()])->filter()->implode(' · ') }}
                                </p>
                                @if ($item->penerimaan !== null || $item->belanja !== null)
                                    <dl class="mt-3 flex flex-wrap gap-x-6 gap-y-1 text-sm">
                                        @if ($item->penerimaan !== null)
                                            <div><dt class="inline text-slate-600">Penerimaan:</dt> <dd class="inline font-bold text-slate-900">{{ DokumenAnggaran::rupiah($item->penerimaan) }}</dd></div>
                                        @endif
                                        @if ($item->belanja !== null)
                                            <div><dt class="inline text-slate-600">Penggunaan:</dt> <dd class="inline font-bold text-slate-900">{{ DokumenAnggaran::rupiah($item->belanja) }}</dd></div>
                                        @endif
                                        @if ($item->sisa() !== null)
                                            <div><dt class="inline text-slate-600">Sisa:</dt> <dd class="inline font-bold text-slate-900">{{ DokumenAnggaran::rupiah($item->sisa()) }}</dd></div>
                                        @endif
                                    </dl>
                                @endif
                                @if ($item->keterangan)
                                    <p class="mt-2 text-sm text-slate-700">{{ $item->keterangan }}</p>
                                @endif
                            </div>
                            <a href="{{ $item->url() }}" target="_blank" rel="noopener" class="tombol-kedua shrink-0">
                                <x-heroicon-o-document-text class="h-4 w-4" aria-hidden="true" /> Lihat dokumen
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        <p class="text-sm text-slate-600">
            Ada pertanyaan tentang penggunaan dana sekolah? <a href="{{ route('kontak') }}" class="tautan">Kirim pertanyaan</a> atau datang langsung ke sekolah pada jam layanan.
        </p>
    </div>
</x-layouts.publik>
