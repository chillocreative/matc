<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
use App\Enums\CategoryType;
use App\Models\Attendance;
use App\Models\Meeting;
use App\Models\Member;
use App\Services\MeetingService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly MeetingService $meetingService,
    ) {}

    public function __invoke(): Response
    {
        $latestMeeting = Meeting::latest('date')->first();

        return Inertia::render('Dashboard', [
            'stats' => [
                'matc' => $this->countByCategory(CategoryType::Matc, $latestMeeting?->id),
                'amk' => $this->countByCategory(CategoryType::Amk, $latestMeeting?->id),
                'wanita' => $this->countByCategory(CategoryType::Wanita, $latestMeeting?->id),
            ],
            'latest_meeting' => $latestMeeting ? [
                'title' => $latestMeeting->title,
                'date'  => $latestMeeting->date->format('d/m/Y'),
            ] : null,
            'upcoming_meetings' => $this->meetingService->upcoming(5),
        ]);
    }

    private function countByCategory(CategoryType $category, ?int $meetingId): array
    {
        if (! $meetingId) {
            return ['hadir' => 0, 'tidak_hadir' => 0];
        }

        $base = Attendance::where('meeting_id', $meetingId)
            ->where('category_type', $category->value);

        return [
            'hadir' => (clone $base)->where('status', AttendanceStatus::Present->value)->count(),
            'tidak_hadir' => (clone $base)->where('status', AttendanceStatus::Absent->value)->count(),
        ];
    }
}
