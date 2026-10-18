<div class="wadah">
    <x-kepala-bagian :bagian="$bagian" :tautan="route('agenda')" label-tautan="Semua agenda" />

    <ul class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($agenda as $item)
            <li class="kartu flex flex-col p-5">
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-extrabold leading-none text-slate-900">{{ $item->mulai->format('j') }}</span>
                    <span class="text-sm font-bold text-slate-600">{{ $item->mulai->translatedFormat('F Y') }}</span>
                </div>
                <p class="mt-4 font-extrabold leading-snug text-slate-900">{{ $item->judul }}</p>
                <p class="mt-1 text-sm text-slate-600">{{ collect([$item->tanggalTeks(), $item->waktu])->filter()->implode(' · ') }}</p>
                @if ($item->libur)
                    <span class="lencana-aksen mt-3 w-fit">Libur</span>
                @endif
            </li>
        @endforeach
    </ul>
</div>
