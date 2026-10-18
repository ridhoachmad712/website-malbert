<?php

use App\Http\Controllers\PublikController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

// Panel admin ada di /admin (Filament). Semua rute di bawah ini adalah halaman publik.
Route::controller(PublikController::class)->group(function () {
    Route::get('/', 'beranda')->name('beranda');

    Route::get('/profil', 'profil')->name('profil');
    Route::get('/profil/guru', 'guru')->name('guru');
    Route::get('/profil/guru/{guru:slug}', 'guruDetail')->name('guru.detail');
    Route::get('/profil/{halaman:slug}', 'halaman')->name('halaman');

    Route::get('/berita', 'berita')->name('berita');
    Route::get('/berita/{berita:slug}', 'beritaDetail')->name('berita.detail');
    Route::get('/agenda', 'agenda')->name('agenda');
    Route::get('/galeri', 'galeri')->name('galeri');
    Route::get('/galeri/{album:slug}', 'galeriDetail')->name('galeri.detail');
    Route::get('/prestasi', 'prestasi')->name('prestasi');
    Route::get('/unduhan', 'unduhan')->name('unduhan');

    Route::get('/tebak-aksi', 'tebakAksi')->name('tebakaksi');
    Route::get('/spmb', 'spmb')->name('spmb');

    Route::get('/kontak', 'kontak')->name('kontak');
    Route::post('/kontak', 'kirimPesan')->middleware('throttle:5,10')->name('kontak.kirim');
});

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
