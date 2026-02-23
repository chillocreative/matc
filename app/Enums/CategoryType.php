<?php

declare(strict_types=1);

namespace App\Enums;

enum CategoryType: string
{
    case Matc = 'matc';
    case Amk = 'amk';
    case Wanita = 'wanita';

    public function label(): string
    {
        return match ($this) {
            self::Matc => 'MATC',
            self::Amk => 'MATAMKC',
            self::Wanita => 'MATWC',
        };
    }

    public function slug(): string
    {
        return match ($this) {
            self::Matc => 'matc',
            self::Amk => 'amk',
            self::Wanita => 'wanita',
        };
    }

    public static function fromSlug(string $slug): self
    {
        return match ($slug) {
            'matc' => self::Matc,
            'amk' => self::Amk,
            'wanita' => self::Wanita,
            default => throw new \ValueError("Invalid category slug: {$slug}"),
        };
    }

    /**
     * @return array<string>
     */
    public function positions(): array
    {
        return match ($this) {
            self::Matc => [
                'Anggota Biasa',
                'Ketua Cabang',
                'Timbalan Ketua Cabang',
                'Naib Ketua Cabang',
                'Setiausaha',
                'Setiausaha Pengelola',
                'Bendahari',
                'Ketua Penerangan',
                'AJK',
            ],
            self::Amk => [
                'Anggota Biasa',
                'Ketua AMK',
                'Timbalan Ketua AMK',
                'Naib Ketua AMK',
                'Setiausaha',
                'Bendahari',
                'Ketua Penerangan',
                'AJK',
            ],
            self::Wanita => [
                'Anggota Biasa',
                'Ketua Wanita',
                'Timbalan Ketua Wanita',
                'Naib Ketua Wanita',
                'Setiausaha',
                'Bendahari',
                'Ketua Penerangan',
                'AJK',
            ],
        };
    }

    /**
     * @return array<string>
     */
    public static function allPositions(): array
    {
        return array_values(array_unique(array_merge(
            self::Matc->positions(),
            self::Amk->positions(),
            self::Wanita->positions(),
        )));
    }
}
