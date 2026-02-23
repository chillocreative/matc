<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\CategoryType;
use App\Http\Requests\Member\StoreMemberRequest;
use App\Http\Requests\Member\UpdateMemberRequest;
use App\Models\Member;
use App\Services\ActivityLogService;
use App\Services\MemberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MemberController extends Controller
{
    public function __construct(
        private readonly MemberService $service,
        private readonly ActivityLogService $logger,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Member::class);

        return Inertia::render('Member/Index', [
            'members' => $this->service->list(
                $request->query('search'),
                15,
                $request->query('category'),
            ),
            'filters' => $request->only('search', 'category'),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Member::class);

        return Inertia::render('Member/Create', [
            'categories' => CategoryType::cases(),
        ]);
    }

    public function store(StoreMemberRequest $request): RedirectResponse
    {
        $member = $this->service->create($request->validated());

        $this->logger->log('created', $member, "Ahli ditambah: {$member->name}");

        return redirect()->route('members.index')
            ->with('success', 'Ahli berjaya ditambah.');
    }

    public function show(Member $member): Response
    {
        $this->authorize('view', $member);

        $member->load('attendances.meeting');

        return Inertia::render('Member/Show', [
            'member' => $member,
        ]);
    }

    public function edit(Member $member): Response
    {
        $this->authorize('update', $member);

        return Inertia::render('Member/Edit', [
            'member' => $member,
            'categories' => CategoryType::cases(),
        ]);
    }

    public function update(UpdateMemberRequest $request, Member $member): RedirectResponse
    {
        $this->service->update($member->id, $request->validated());

        $this->logger->log('updated', $member, "Ahli dikemaskini: {$member->name}");

        return redirect()->route('members.index')
            ->with('success', 'Ahli berjaya dikemaskini.');
    }

    public function destroy(Member $member): RedirectResponse
    {
        $this->authorize('delete', $member);

        $this->logger->log('deleted', $member, "Ahli dipadam: {$member->name}");

        $this->service->delete($member->id);

        return redirect()->route('members.index')
            ->with('success', 'Ahli berjaya dipadam.');
    }
}
