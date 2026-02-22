<?php

declare(strict_types=1);

namespace App\Support;

class IcHasher
{
    public static function hash(string $icNumber): string
    {
        $normalized = preg_replace('/[^0-9]/', '', $icNumber);

        return hash('sha256', $normalized);
    }
}
