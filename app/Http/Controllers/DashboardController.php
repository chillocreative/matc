<?php

declare(strict_types=1);

namespace App\Http\Controllers;

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
        return Inertia::render('Dashboard', [
            'stats' => [
                'total_members' => Member::count(),
                'total_meetings' => Meeting::count(),
                'total_attendances' => Attendance::count(),
                'attendance_rate' => $this->calculateAttendanceRate(),
            ],
            'upcoming_meetings' => $this->meetingService->upcoming(5),
        ]);
    }

    private function calculateAttendanceRate(): float
    {
        $total = Attendance::count();

        if ($total === 0) {
            return 0;
        }

        $present = Attendance::where('status', 'present')->count();

        return round(($present / $total) * 100, 1);
    }
}
