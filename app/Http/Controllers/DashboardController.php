<?php

declare(strict_types=1);

namespace App\Http\Controllers;

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
        return Inertia::render('Dashboard', [
            'stats' => [
                'matc' => $this->countByCategory(CategoryType::Matc),
                'amk' => $this->countByCategory(CategoryType::Amk),
                'wanita' => $this->countByCategory(CategoryType::Wanita),
            ],
            'upcoming_meetings' => $this->meetingService->upcoming(5),
        ]);
    }

    private function countByCategory(CategoryType $category): int
    {
        return Attendance::whereHas('member', fn ($q) => $q->where('category_type', $category->value))->count();
    }
}
