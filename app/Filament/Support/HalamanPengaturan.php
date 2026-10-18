<?php

namespace App\Filament\Support;

use App\Models\Pengaturan;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Dasar halaman admin yang mengisi Pengaturan (bukan tabel biasa).
 * Turunannya menyebut kunci yang dikelola, mana yang berupa daftar, saklar, dan berkas.
 */
abstract class HalamanPengaturan extends Page
{
    /** @var array<int, string> */
    protected static array $kunci = [];

    /** Kunci berupa daftar (Repeater). */
    protected static array $daftar = [];

    /** Kunci berupa saklar ya/tidak (Toggle). */
    protected static array $saklar = [];

    /** Kunci berupa berkas unggahan; berkas lama dihapus saat diganti. */
    protected static array $berkas = [];

    protected static string $pesanTersimpan = 'Perubahan disimpan';

    public ?array $data = [];

    public function mount(): void
    {
        $nilai = [];
        foreach (static::$kunci as $kunci) {
            $nilai[$kunci] = match (true) {
                in_array($kunci, static::$daftar, true) => Pengaturan::daftar($kunci),
                in_array($kunci, static::$saklar, true) => Pengaturan::aktif($kunci),
                default => Pengaturan::ambil($kunci) ?: null,
            };
        }

        $this->form->fill($nilai);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('simpan')
                ->footer([Actions::make([
                    Action::make('simpan')->label('Simpan')->submit('simpan'),
                    ...$this->aksiTambahan(),
                ])]),
        ]);
    }

    /** Tombol lain di samping Simpan, misalnya "Lihat di website". */
    protected function aksiTambahan(): array
    {
        return [];
    }

    public function simpan(): void
    {
        $data = array_intersect_key($this->form->getState(), array_flip(static::$kunci));

        foreach (static::$berkas as $kunci) {
            $lama = Pengaturan::ambil($kunci);
            if (filled($lama) && $lama !== ($data[$kunci] ?? null)) {
                Storage::disk('public')->delete($lama);
            }
        }
        foreach (static::$saklar as $kunci) {
            $data[$kunci] = (bool) ($data[$kunci] ?? false);
        }

        Pengaturan::simpan($data);

        Notification::make()->success()->title(static::$pesanTersimpan)->send();
    }
}
