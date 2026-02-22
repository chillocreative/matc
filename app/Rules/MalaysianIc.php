<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MalaysianIc implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $digits = preg_replace('/[^0-9]/', '', (string) $value);

        if (strlen($digits) !== 12) {
            $fail('No. IC mestilah 12 digit.');

            return;
        }

        $month = (int) substr($digits, 2, 2);
        $day = (int) substr($digits, 4, 2);

        if ($month < 1 || $month > 12) {
            $fail('Format No. IC tidak sah — bulan tidak wujud.');

            return;
        }

        if ($day < 1 || $day > 31) {
            $fail('Format No. IC tidak sah — hari tidak wujud.');

            return;
        }

        $state = (int) substr($digits, 6, 2);

        $validState = ($state >= 1 && $state <= 16)
            || ($state >= 21 && $state <= 59)
            || ($state >= 60 && $state <= 76)
            || ($state >= 82 && $state <= 93)
            || $state === 98 || $state === 99;

        if (! $validState) {
            $fail('Kod negeri dalam No. IC tidak sah.');
        }
    }
}
