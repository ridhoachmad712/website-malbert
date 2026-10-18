#!/usr/bin/env bash
# Cadangan harian database dan berkas unggahan website, untuk Cron Job di hPanel:
#   bash /home/USER/website-sdimalbert/scripts/cadangan-harian.sh
#
# Cron hPanel menjalankan perintah shell, jadi tidak terhalang fungsi exec() yang dimatikan di PHP.
# Menyimpan 14 cadangan terakhir di ~/cadangan-website (di luar web root, tidak bisa diakses dari internet).

set -euo pipefail

APLIKASI="${APLIKASI:-$HOME/website-sdimalbert}"
TUJUAN="${TUJUAN:-$HOME/cadangan-website}"
SIMPAN=14

baca_env() {
    grep -E "^$1=" "$APLIKASI/.env" | tail -n1 | cut -d= -f2- | sed -e 's/^"//' -e 's/"$//'
}

DB_HOST="$(baca_env DB_HOST)"
DB_PORT="$(baca_env DB_PORT)"
DB_DATABASE="$(baca_env DB_DATABASE)"
DB_USERNAME="$(baca_env DB_USERNAME)"
DB_PASSWORD="$(baca_env DB_PASSWORD)"

mkdir -p "$TUJUAN"
chmod 700 "$TUJUAN"
WAKTU="$(date +%Y%m%d-%H%M)"

# Kata sandi lewat berkas sementara agar tidak terlihat di daftar proses
KONFIG="$(mktemp)"
trap 'rm -f "$KONFIG"' EXIT
printf '[client]\nuser=%s\npassword=%s\nhost=%s\nport=%s\n' "$DB_USERNAME" "$DB_PASSWORD" "${DB_HOST:-localhost}" "${DB_PORT:-3306}" > "$KONFIG"

mysqldump --defaults-extra-file="$KONFIG" --single-transaction --no-tablespaces "$DB_DATABASE" | gzip > "$TUJUAN/data-$WAKTU.sql.gz"
tar -czf "$TUJUAN/berkas-$WAKTU.tar.gz" -C "$APLIKASI/storage/app" public

# Hapus cadangan lama, sisakan $SIMPAN terbaru untuk masing-masing jenis
for jenis in data berkas; do
    ls -1t "$TUJUAN"/$jenis-* 2>/dev/null | tail -n +$((SIMPAN + 1)) | xargs -r rm -f
done

echo "Cadangan $WAKTU selesai."
