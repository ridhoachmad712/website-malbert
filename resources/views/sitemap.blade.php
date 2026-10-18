{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($alamat as $item)
    <url>
        <loc>{{ $item['loc'] }}</loc>
@if ($item['lastmod'])
        <lastmod>{{ $item['lastmod']->toAtomString() }}</lastmod>
@endif
    </url>
@endforeach
</urlset>
