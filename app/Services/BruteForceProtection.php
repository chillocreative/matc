<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class BruteForceProtection
{
    private const MAX_ATTEMPTS = 5;

    private const DECAY_MINUTES = 15;

    private const BLOCK_MINUTES = 30;

    public function isBlocked(string $key): bool
    {
        return Cache::has($this->blockKey($key));
    }

    public function remainingSeconds(string $key): int
    {
        $until = Cache::get($this->blockKey($key));

        if (! $until) {
            return 0;
        }

        return max(0, $until - now()->timestamp);
    }

    public function recordFailedAttempt(string $key): void
    {
        $attemptsKey = $this->attemptsKey($key);
        $attempts = (int) Cache::get($attemptsKey, 0) + 1;

        Cache::put($attemptsKey, $attempts, now()->addMinutes(self::DECAY_MINUTES));

        if ($attempts >= self::MAX_ATTEMPTS) {
            Cache::put(
                $this->blockKey($key),
                now()->addMinutes(self::BLOCK_MINUTES)->timestamp,
                now()->addMinutes(self::BLOCK_MINUTES),
            );
        }
    }

    public function clearAttempts(string $key): void
    {
        Cache::forget($this->attemptsKey($key));
        Cache::forget($this->blockKey($key));
    }

    private function attemptsKey(string $key): string
    {
        return "bf_attempts:{$key}";
    }

    private function blockKey(string $key): string
    {
        return "bf_blocked:{$key}";
    }
}
