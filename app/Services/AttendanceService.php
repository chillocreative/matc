<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Enums\CategoryType;
use App\Models\Attendance;
use App\Repositories\AttendanceRepository;
use App\Repositories\Contracts\MemberRepositoryInterface;
use App\Support\IcHasher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    public function __construct(
        private readonly AttendanceRepository $repository,
        private readonly MemberRepositoryInterface $memberRepository,
        private readonly ActivityLogService $logger,
    ) {}

    public function getByMeeting(int $meetingId): Collection
    {
        return $this->repository->getByMeeting($meetingId);
    }

    public function getByMeetingFiltered(int $meetingId, ?string $category): Collection
    {
        return $this->repository->getByMeetingFiltered($meetingId, $category);
    }

    public function markAttendance(int $meetingId, array $attendances): void
    {
        DB::transaction(function () use ($meetingId, $attendances): void {
            foreach ($attendances as $attendance) {
                $member = $this->memberRepository->findByIdForUpdate($attendance['member_id']);

                $this->repository->upsertForMeetingAndMember(
                    $meetingId,
                    $member->id,
                    [
                        'ic_number_hash' => IcHasher::hash($member->ic_number),
                        'status' => $attendance['status'],
                    ],
                );
            }
        });

        $this->logger->log('marked_attendance', null, "Kehadiran direkod untuk mesyuarat #{$meetingId}");
    }

    public function verifyByIc(int $meetingId, string $icNumber): Attendance
    {
        $attendance = $this->performVerification($meetingId, $icNumber);

        $this->logger->log('verified_attendance', $attendance, 'Pengesahan kehadiran via IC');

        return $attendance;
    }

    /**
     * @param  array{name: string, phone_number: ?string, address: ?string, position_type: ?string}  $memberData
     */
    public function publicVerifyByIc(int $meetingId, string $icNumber, CategoryType $category, array $memberData = [], AttendanceStatus $status = AttendanceStatus::Present): Attendance
    {
        return DB::transaction(function () use ($meetingId, $icNumber, $category, $memberData, $status): Attendance {
            $icHash = IcHasher::hash($icNumber);

            $member = $this->memberRepository->findByIcHashForUpdateOrNull($icHash);

            if ($member) {
                if ($member->category_type !== $category) {
                    throw new \DomainException('Kategori ahli tidak sepadan.');
                }

                $updateData = array_filter([
                    'phone_number' => $memberData['phone_number'] ?? null,
                    'address' => $memberData['address'] ?? null,
                ]);

                if ($updateData) {
                    $member->update($updateData);
                }
            } else {
                $member = $this->memberRepository->create([
                    'category_type' => $category->value,
                    'name' => $memberData['name'],
                    'ic_number' => $icNumber,
                    'phone_number' => $memberData['phone_number'] ?? null,
                    'address' => $memberData['address'] ?? null,
                    'position_type' => $memberData['position_type'] ?? null,
                ]);
            }

            if ($this->repository->existsForMeetingAndIcHash($meetingId, $icHash)) {
                throw new \DomainException('IC ini sudah membuat pengesahan untuk mesyuarat ini.');
            }

            return $this->repository->createRecord([
                'meeting_id' => $meetingId,
                'member_id' => $member->id,
                'ic_number_hash' => $icHash,
                'status' => $status->value,
            ]);
        });
    }

    public function isVerified(int $meetingId, string $icNumber): bool
    {
        return $this->repository->existsForMeetingAndIcHash(
            $meetingId,
            IcHasher::hash($icNumber),
        );
    }

    private function performVerification(int $meetingId, string $icNumber): Attendance
    {
        return DB::transaction(function () use ($meetingId, $icNumber): Attendance {
            $icHash = IcHasher::hash($icNumber);

            $member = $this->memberRepository->findByIcHashForUpdate($icHash);

            if ($this->repository->existsForMeetingAndIcHash($meetingId, $icHash)) {
                throw new UniqueConstraintViolationException(
                    'mysql',
                    'IC sudah membuat pengesahan untuk mesyuarat ini.',
                    [],
                    new \Exception('IC sudah membuat pengesahan untuk mesyuarat ini.'),
                );
            }

            return $this->repository->createRecord([
                'meeting_id' => $meetingId,
                'member_id' => $member->id,
                'ic_number_hash' => $icHash,
                'status' => AttendanceStatus::Present->value,
            ]);
        });
    }
}
