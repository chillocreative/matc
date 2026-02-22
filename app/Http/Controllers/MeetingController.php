<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\MeetingStatus;
use App\Http\Requests\Meeting\StoreMeetingRequest;
use App\Http\Requests\Meeting\UpdateMeetingRequest;
use App\Models\Meeting;
use App\Services\ActivityLogService;
use App\Services\MeetingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MeetingController extends Controller
{
    public function __construct(
        private readonly MeetingService $service,
        private readonly ActivityLogService $logger,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Meeting::class);

        return Inertia::render('Meeting/Index', [
            'meetings' => $this->service->list($request->query('search'), 15),
            'filters' => $request->only('search'),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Meeting::class);

        return Inertia::render('Meeting/Create', [
            'statuses' => MeetingStatus::cases(),
        ]);
    }

    public function store(StoreMeetingRequest $request): RedirectResponse
    {
        $meeting = $this->service->create($request->validated());

        $this->logger->log('created', $meeting, "Mesyuarat ditambah: {$meeting->title}");

        return redirect()->route('meetings.index')
            ->with('success', 'Mesyuarat berjaya ditambah.');
    }

    public function show(Meeting $meeting): Response
    {
        $this->authorize('view', $meeting);

        $meeting->load('attendances.member');

        return Inertia::render('Meeting/Show', [
            'meeting' => $meeting,
        ]);
    }

    public function edit(Meeting $meeting): Response
    {
        $this->authorize('update', $meeting);

        return Inertia::render('Meeting/Edit', [
            'meeting' => $meeting,
            'statuses' => MeetingStatus::cases(),
        ]);
    }

    public function update(UpdateMeetingRequest $request, Meeting $meeting): RedirectResponse
    {
        $this->service->update($meeting->id, $request->validated());

        $this->logger->log('updated', $meeting, "Mesyuarat dikemaskini: {$meeting->title}");

        return redirect()->route('meetings.index')
            ->with('success', 'Mesyuarat berjaya dikemaskini.');
    }

    public function destroy(Meeting $meeting): RedirectResponse
    {
        $this->authorize('delete', $meeting);

        $this->logger->log('deleted', $meeting, "Mesyuarat dipadam: {$meeting->title}");

        $this->service->delete($meeting->id);

        return redirect()->route('meetings.index')
            ->with('success', 'Mesyuarat berjaya dipadam.');
    }
}
