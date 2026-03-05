<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\CategoryType;
use App\Models\Attendance;
use App\Models\Member;
use App\Services\AttendanceService;
use App\Services\MeetingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ExportController extends Controller
{
    public function __construct(
        private readonly AttendanceService $attendanceService,
        private readonly MeetingService $meetingService,
    ) {
        File::ensureDirectoryExists(storage_path('fonts'));
    }

    public function attendancePdf(Request $request): Response
    {
        $this->authorize('viewAny', Attendance::class);

        $request->validate([
            'meeting_id' => 'required|integer|exists:meetings,id',
            'category' => 'required|string|in:matc,amk,wanita',
        ]);

        $meetingId = (int) $request->query('meeting_id');
        $category = $request->query('category');

        $meeting = $this->meetingService->find($meetingId);
        $attendances = $this->attendanceService->getByMeetingFiltered($meetingId, $category);
        $categoryEnum = CategoryType::fromSlug($category);

        $rows = $attendances->map(fn ($a) => [
            'name' => $a->member?->name,
            'ic_number' => $a->member?->ic_number,
            'position_type' => $a->member?->position_type,
            'phone_number' => $a->member?->phone_number,
            'address' => $a->member?->address,
            'status' => $a->status?->label(),
            'absence_reason' => $a->absence_reason,
            'suggestion' => $a->suggestion,
        ])->sortBy('name')->values()->toArray();

        $dateFormatted = $meeting->date->format('d-m-Y');

        try {
            $pdf = Pdf::loadView('pdf.attendance', [
                'meetingTitle' => $meeting->title,
                'meetingDate' => $dateFormatted,
                'categoryLabel' => $categoryEnum->label(),
                'rows' => $rows,
            ])->setPaper('a4', 'landscape');

            return $pdf->download("kehadiran-{$category}-{$dateFormatted}.pdf");
        } catch (\Throwable $e) {
            Log::error('PDF generation failed', ['error' => $e->getMessage()]);

            return redirect()->back()->with('error', 'Gagal menjana PDF. Sila cuba lagi.');
        }
    }

    public function membersPdf(Request $request): Response
    {
        $this->authorize('viewAny', Member::class);

        $request->validate([
            'category' => 'required|string|in:matc,amk,wanita',
        ]);

        $category = $request->query('category');
        $categoryEnum = CategoryType::fromSlug($category);

        $members = Member::where('category_type', $category)
            ->orderBy('name')
            ->get();

        $rows = $members->map(fn ($m) => [
            'name' => $m->name,
            'ic_number' => $m->ic_number,
            'phone_number' => $m->phone_number,
            'address' => $m->address,
        ])->toArray();

        $pdf = Pdf::loadView('pdf.members', [
            'categoryLabel' => $categoryEnum->label(),
            'generatedAt' => now()->format('d/m/Y H:i'),
            'rows' => $rows,
        ])->setPaper('a4', 'landscape');

        return $pdf->download("ahli-{$category}.pdf");
    }
}
