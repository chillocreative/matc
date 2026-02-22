<?php

declare(strict_types=1);

namespace App\Enums;

enum CategoryType: string
{
    case Anggota = 'anggota';
    case AjkCabang = 'ajk_cabang';
    case Amk = 'amk';
    case Wanita = 'wanita';

    public function label(): string
    {
        return match ($this) {
            self::Anggota => 'Anggota',
            self::AjkCabang => 'AJK Cabang',
            self::Amk => 'AMK',
            self::Wanita => 'Wanita',
        };
    }

    public function slug(): string
    {
        return match ($this) {
            self::Anggota => 'anggota',
            self::AjkCabang => 'ajk-cabang',
            self::Amk => 'amk',
            self::Wanita => 'wanita',
        };
    }

    public static function fromSlug(string $slug): self
    {
        return match ($slug) {
            'anggota' => self::Anggota,
            'ajk-cabang', 'ajk_cabang' => self::AjkCabang,
            'amk' => self::Amk,
            'wanita' => self::Wanita,
            default => throw new \ValueError("Invalid category slug: {$slug}"),
        };
    }
}
