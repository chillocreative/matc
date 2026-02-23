<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Member;
use Illuminate\Pagination\LengthAwarePaginator;

interface MemberRepositoryInterface extends RepositoryInterface
{
    public function search(string $query, int $perPage = 15, ?string $category = null): LengthAwarePaginator;

    public function paginateByCategory(string $category, int $perPage = 15): LengthAwarePaginator;

    public function findByIdForUpdate(int $id): Member;

    public function findByIcHashForUpdate(string $icHash): Member;

    public function findByIcHashForUpdateOrNull(string $icHash): ?Member;
}
