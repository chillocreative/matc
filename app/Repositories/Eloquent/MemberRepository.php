<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Member;
use App\Repositories\Contracts\MemberRepositoryInterface;
use App\Support\IcHasher;
use Illuminate\Pagination\LengthAwarePaginator;

class MemberRepository extends BaseRepository implements MemberRepositoryInterface
{
    public function __construct(Member $model)
    {
        parent::__construct($model);
    }

    public function search(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->where('name', 'like', "%{$query}%")
            ->orWhere('ic_number_hash', IcHasher::hash($query))
            ->latest('created_at')
            ->paginate($perPage);
    }

    public function findByIdForUpdate(int $id): Member
    {
        return $this->model
            ->where('id', $id)
            ->lockForUpdate()
            ->firstOrFail();
    }

    public function findByIcHashForUpdate(string $icHash): Member
    {
        return $this->model
            ->where('ic_number_hash', $icHash)
            ->lockForUpdate()
            ->firstOrFail();
    }

    public function findByIcHashForUpdateOrNull(string $icHash): ?Member
    {
        return $this->model
            ->where('ic_number_hash', $icHash)
            ->lockForUpdate()
            ->first();
    }
}
