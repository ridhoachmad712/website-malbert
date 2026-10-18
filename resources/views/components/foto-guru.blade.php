@props(['guru', 'besar' => false])

@if ($foto = $guru->urlFoto())
    <img src="{{ $foto }}" alt="Foto {{ $guru->nama }}" loading="lazy" {{ $attributes->merge(['class' => 'aspect-[3/4] w-full rounded-lg object-cover bg-krem']) }}>
@else
    <div {{ $attributes->merge(['class' => 'grid aspect-[3/4] w-full place-items-center rounded-lg bg-brand-50 font-extrabold text-brand-800 '.($besar ? 'text-5xl' : 'text-3xl')]) }} aria-hidden="true">{{ $guru->inisial() }}</div>
@endif
