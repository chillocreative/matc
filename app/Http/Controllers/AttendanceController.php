<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
use App\Http\Requests\Attendance\StoreAttendanceRequest;
use App\Http\Requests\Attendance\VerifyAttendanceRequest;
use App\Models\Attendance;
use App\Services\AttendanceService;
use App\Services\MeetingService;
use App\Services\MemberService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AttendanceController extends Controller
{
    public function __construct(
        private readonly AttendanceService $attendanceService,
        private readonly MeetingService $meetingService,
        private readonly MemberService $memberService,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Attendance::class);

        $meetingId = $request->query('meeting_id');
        $category = $request->query('category');
        $meeting = $meetingId ? $this->meetingService->find((int) $meetingId) : null;
        $attendances = $meetingId
            ? $this->attendanceService->getByMeetingFiltered((int) $meetingId, $category)
            : collect();

        return Inertia::render('Attendance/Index', [
            'meetings' => $this->meetingService->list(null, 100),
            'meeting' => $meeting,
            'attendances' => $attendances,
            'filters' => $request->only('meeting_id', 'category'),
        ]);
    }

    public function verifyForm(int $meetingId): Response
    {
        $this->authorize('create', Attendance::class);

        $meeting = $this->meetingService->find($meetingId);
        $attendances = $this->attendanceService->getByMeeting($meetingId);

        return Inertia::render('Attendance/Verify', [
            'meeting' => $meeting,
            'attendances' => $attendances,
        ]);
    }

    public function mark(int $meetingId): Response
    {
        $this->authorize('create', Attendance::class);

        $meeting = $this->meetingService->find($meetingId);
        $members = $this->memberService->list(null, 500);
        $attendances = $this->attendanceService->getByMeeting($meetingId);

        return Inertia::render('Attendance/Mark', [
            'meeting' => $meeting,
            'members' => $members,
            'attendances' => $attendances,
            'statuses' => AttendanceStatus::cases(),
        ]);
    }

    public function store(StoreAttendanceRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $this->attendanceService->markAttendance(
            (int) $validated['meeting_id'],
            $validated['attendances']
        );

        return redirect()->route('attendances.index', ['meeting_id' => $validated['meeting_id']])
            ->with('success', 'Kehadiran berjaya direkod.');
    }

    public function verify(VerifyAttendanceRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $this->attendanceService->verifyByIc(
                (int) $validated['meeting_id'],
                $validated['ic_number']
            );

            return redirect()->back()
                ->with('success', 'Pengesahan kehadiran berjaya.');
        } catch (UniqueConstraintViolationException) {
            return redirect()->back()
                ->with('error', 'IC ini sudah membuat pengesahan untuk mesyuarat ini.');
        }
    }
}
