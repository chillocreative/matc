<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Meeting;
use App\Repositories\Contracts\MeetingRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class MeetingRepository extends BaseRepository implements MeetingRepositoryInterface
{
    public function __construct(Meeting $model)
    {
        parent::__construct($model);
    }

    public function search(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->where('title', 'like', "%{$query}%")
            ->orWhere('location', 'like', "%{$query}%")
            ->latest('date')
            ->paginate($perPage);
    }

    public function upcoming(int $limit = 5): Collection
    {
        return $this->model->where('date', '>=', now())
            ->orderBy('date')
            ->limit($limit)
            ->get();
    }
}
