<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface MeetingRepositoryInterface extends RepositoryInterface
{
    public function search(string $query, int $perPage = 15): LengthAwarePaginator;

    public function upcoming(int $limit = 5): \Illuminate\Database\Eloquent\Collection;
}
