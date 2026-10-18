// Menu tarik-turun memakai <details>, jadi tetap berfungsi tanpa JavaScript.
// Skrip ini hanya menutup menu yang terbuka saat pengunjung mengetuk di luar menu atau menekan Esc.
const menu = () => document.querySelectorAll('details[data-menu][open]');

document.addEventListener('click', (event) => {
    menu().forEach((details) => {
        if (!details.contains(event.target)) details.removeAttribute('open');
    });
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        menu().forEach((details) => {
            details.removeAttribute('open');
            details.querySelector('summary')?.focus();
        });
    }
});

// Satu menu terbuka dalam satu waktu
document.querySelectorAll('details[data-menu]').forEach((details) => {
    details.addEventListener('toggle', () => {
        if (!details.open) return;
        document.querySelectorAll('details[data-menu][open]').forEach((lain) => {
            if (lain !== details && !lain.contains(details)) lain.removeAttribute('open');
        });
    });
});

// Penampil foto galeri: tautan dengan atribut data-penampil membuka foto besar tanpa meninggalkan halaman
const penampil = document.querySelector('dialog.penampil');

if (penampil) {
    const tautan = [...document.querySelectorAll('a[data-penampil]')];
    const gambar = penampil.querySelector('img');
    const keterangan = penampil.querySelector('[data-keterangan]');
    const nomor = penampil.querySelector('[data-nomor]');
    let posisi = 0;

    const tampilkan = (indeks) => {
        posisi = (indeks + tautan.length) % tautan.length;
        const a = tautan[posisi];
        gambar.src = a.href;
        gambar.alt = a.dataset.keterangan || 'Foto kegiatan';
        keterangan.textContent = a.dataset.keterangan || '';
        nomor.textContent = `Foto ${posisi + 1} dari ${tautan.length}`;
    };

    tautan.forEach((a, indeks) => {
        a.addEventListener('click', (event) => {
            event.preventDefault();
            tampilkan(indeks);
            penampil.showModal();
        });
    });

    penampil.querySelector('[data-sebelumnya]')?.addEventListener('click', () => tampilkan(posisi - 1));
    penampil.querySelector('[data-berikutnya]')?.addEventListener('click', () => tampilkan(posisi + 1));
    penampil.querySelector('[data-tutup]')?.addEventListener('click', () => penampil.close());
    penampil.addEventListener('click', (event) => {
        if (event.target === penampil) penampil.close();
    });
    penampil.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowLeft') tampilkan(posisi - 1);
        if (event.key === 'ArrowRight') tampilkan(posisi + 1);
    });
    penampil.addEventListener('close', () => {
        gambar.removeAttribute('src');
        tautan[posisi]?.focus();
    });
}
