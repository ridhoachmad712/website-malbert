#!/usr/bin/env bash
# Menghubungkan web root domain utama (public_html) ke folder public website ini,
# TANPA menyentuh folder lain di public_html (misalnya symlink `tebakaksi` milik aplikasi TEBAK AKSI).
#
# Pemakaian (di server, lewat SSH):
#   bash ~/website-sdimalbert/scripts/pasang-public-html.sh
#
# Aman dijalankan berulang setelah setiap deploy. Jalankan juga bila ada folder baru di public/.

set -euo pipefail

APLIKASI="${APLIKASI:-$HOME/website-sdimalbert}"
WEBROOT="${WEBROOT:-$HOME/domains/sdimalbert.sch.id/public_html}"

# Isi public_html yang bukan milik website ini dan tidak boleh diubah
DILINDUNGI=("tebakaksi" "tebakaksi_lama")

if [ ! -f "$APLIKASI/artisan" ]; then
    echo "Folder aplikasi tidak ditemukan: $APLIKASI" >&2
    exit 1
fi
mkdir -p "$WEBROOT"

dilindungi() {
    local nama="$1"
    for d in "${DILINDUNGI[@]}"; do
        [ "$nama" = "$d" ] && return 0
    done
    return 1
}

# 1. Pindahkan sisa website lama ke arsip (sekali saja), kecuali yang dilindungi
ARSIP="$HOME/arsip-public_html-lama-$(date +%Y%m%d)"
for jalur in "$WEBROOT"/* "$WEBROOT"/.htaccess; do
    [ -e "$jalur" ] || [ -L "$jalur" ] || continue
    nama="$(basename "$jalur")"
    dilindungi "$nama" && continue
    # Symlink dan berkas yang sudah dipasang skrip ini dibiarkan; akan diperbarui di langkah 2
    if [ -L "$jalur" ] && [[ "$(readlink "$jalur")" == "$APLIKASI"* ]]; then
        continue
    fi
    if [ "$nama" = ".htaccess" ] && cmp -s "$jalur" "$APLIKASI/public/.htaccess"; then
        continue
    fi
    if [ "$nama" = "index.php" ] && grep -q "Dibuat oleh scripts/pasang-public-html.sh" "$jalur" 2>/dev/null; then
        continue
    fi
    mkdir -p "$ARSIP"
    echo "Arsipkan: $nama -> $ARSIP/"
    mv "$jalur" "$ARSIP/"
done

# 2. Hubungkan setiap isi folder public (kecuali index.php dan .htaccess) dengan symlink
for jalur in "$APLIKASI"/public/*; do
    nama="$(basename "$jalur")"
    [ "$nama" = "index.php" ] && continue
    [ "$nama" = "storage" ] && continue
    dilindungi "$nama" && continue
    ln -sfn "$jalur" "$WEBROOT/$nama"
done
ln -sfn "$APLIKASI/storage/app/public" "$WEBROOT/storage"

# 3. .htaccess disalin (bukan symlink) agar selalu terbaca server
cp "$APLIKASI/public/.htaccess" "$WEBROOT/.htaccess"

# 4. index.php menunjuk ke folder aplikasi
cat > "$WEBROOT/index.php" <<PHP
<?php

// Dibuat oleh scripts/pasang-public-html.sh, website-sdimalbert
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists(\$maintenance = '$APLIKASI/storage/framework/maintenance.php')) {
    require \$maintenance;
}

require '$APLIKASI/vendor/autoload.php';

/** @var Application \$app */
\$app = require_once '$APLIKASI/bootstrap/app.php';

\$app->handleRequest(Request::capture());
PHP

echo "Selesai. Isi $WEBROOT sekarang:"
ls -la "$WEBROOT"
