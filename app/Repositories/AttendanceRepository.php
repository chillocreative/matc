<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Collection;

class AttendanceRepository
{
    public function __construct(
        private readonly Attendance $model,
    ) {}

    public function getByMeeting(int $meetingId): Collection
    {
        return $this->model
            ->where('meeting_id', $meetingId)
            ->with('member')
            ->latest('created_at')
            ->get();
    }

    public function getByMeetingFiltered(int $meetingId, ?string $category): Collection
    {
        return $this->model
            ->where('meeting_id', $meetingId)
            ->with('member')
            ->when($category, fn ($query) => $query->whereHas(
                'member',
                fn ($q) => $q->where('category_type', $category),
            ))
            ->latest('created_at')
            ->get();
    }

    public function existsForMeetingAndIcHash(int $meetingId, string $icHash): bool
    {
        return $this->model
            ->where('meeting_id', $meetingId)
            ->where('ic_number_hash', $icHash)
            ->exists();
    }

    public function existsForMeetingIcAndCategory(int $meetingId, string $icHash, string $category): bool
    {
        return $this->model
            ->where('meeting_id', $meetingId)
            ->where('ic_number_hash', $icHash)
            ->where('category_type', $category)
            ->exists();
    }

    public function upsertForMeetingAndMember(int $meetingId, int $memberId, array $data): Attendance
    {
        return $this->model->updateOrCreate(
            [
                'meeting_id' => $meetingId,
                'member_id' => $memberId,
            ],
            $data,
        );
    }

    public function createRecord(array $data): Attendance
    {
        return $this->model->create($data);
    }

    public function countByMeeting(int $meetingId): int
    {
        return $this->model
            ->where('meeting_id', $meetingId)
            ->count();
    }

    public function countPresentByMeeting(int $meetingId): int
    {
        return $this->model
            ->where('meeting_id', $meetingId)
            ->where('status', 'present')
            ->count();
    }
}
