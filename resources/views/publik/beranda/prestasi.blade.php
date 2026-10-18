<div class="wadah">
    <x-kepala-bagian :bagian="$bagian" :tautan="route('prestasi')" label-tautan="Semua prestasi" />

    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($prestasi as $item)
            <x-kartu-prestasi :prestasi="$item" />
        @endforeach
    </div>
</div>
