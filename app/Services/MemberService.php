<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\MemberRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class MemberService
{
    public function __construct(
        private readonly MemberRepositoryInterface $repository,
    ) {}

    public function list(?string $search, int $perPage = 15, ?string $category = null): LengthAwarePaginator
    {
        if ($search) {
            return $this->repository->search($search, $perPage, $category);
        }

        if ($category) {
            return $this->repository->paginateByCategory($category, $perPage);
        }

        return $this->repository->paginate($perPage);
    }

    public function find(int $id): Model
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): Model
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
