<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\MeetingRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class MeetingService
{
    public function __construct(
        private readonly MeetingRepositoryInterface $repository,
    ) {}

    public function list(?string $search, int $perPage = 15): LengthAwarePaginator
    {
        if ($search) {
            return $this->repository->search($search, $perPage);
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

    public function upcoming(int $limit = 5): Collection
    {
        return $this->repository->upcoming($limit);
    }
}
