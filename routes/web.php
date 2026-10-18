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

    Route::get('/ekstrakurikuler', 'ekskul')->name('ekskul');
    Route::get('/ekstrakurikuler/{ekstrakurikuler:slug}', 'ekskulDetail')->name('ekskul.detail');
    Route::get('/sarana', 'sarana')->name('sarana');
    Route::get('/transparansi', 'transparansi')->name('transparansi');
    Route::get('/alumni', 'alumni')->name('alumni');
    Route::post('/alumni', 'kirimAlumni')->middleware('throttle:3,10')->name('alumni.kirim');
    Route::get('/cari', 'cari')->middleware('throttle:30,1')->name('cari');

    Route::get('/tebak-aksi', 'tebakAksi')->name('tebakaksi');
    Route::get('/spmb', 'spmb')->name('spmb');

    Route::get('/kontak', 'kontak')->name('kontak');
    Route::post('/kontak', 'kirimPesan')->middleware('throttle:5,10')->name('kontak.kirim');
});

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

// Pratinjau berita yang belum terbit, hanya untuk admin yang sudah masuk
Route::get('/pratinjau/berita/{berita:slug}', [PublikController::class, 'pratinjauBerita'])->middleware('auth')->name('berita.pratinjau');
