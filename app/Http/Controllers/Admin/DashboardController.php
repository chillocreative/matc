<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\CategoryType;
use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $categoryCounts = Member::query()
            ->select('category_type', DB::raw('COUNT(*) as total'))
            ->groupBy('category_type')
            ->pluck('total', 'category_type');

        $stats = [
            'total' => $categoryCounts->sum(),
            'anggota' => $categoryCounts->get(CategoryType::Anggota->value, 0),
            'ajk_cabang' => $categoryCounts->get(CategoryType::AjkCabang->value, 0),
            'amk' => $categoryCounts->get(CategoryType::Amk->value, 0),
            'wanita' => $categoryCounts->get(CategoryType::Wanita->value, 0),
        ];

        $query = Member::query()
            ->select('id', 'name', 'category_type', 'phone_number', 'position_type', 'created_at');

        if ($search = $request->string('search')->trim()->value()) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($category = $request->string('category')->trim()->value()) {
            $query->where('category_type', $category);
        }

        $members = $query
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'chartData' => $this->getMonthlyRegistrations(),
            'members' => $members,
            'filters' => [
                'search' => $request->string('search')->value(),
                'category' => $request->string('category')->value(),
            ],
        ]);
    }

    private function getMonthlyRegistrations(): array
    {
        $rows = Member::query()
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                'category_type',
                DB::raw('COUNT(*) as total'),
            )
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('month', 'category_type')
            ->orderBy('month')
            ->get();

        $months = collect(range(0, 11))->map(function (int $i) {
            return now()->subMonths(11 - $i)->format('Y-m');
        });

        $labels = $months->map(function (string $month) {
            $date = \Carbon\Carbon::createFromFormat('Y-m', $month);

            return $date->translatedFormat('M Y');
        })->values()->all();

        $datasets = [];
        $config = [
            CategoryType::Anggota->value => ['label' => 'Anggota', 'color' => '#7c3aed'],
            CategoryType::AjkCabang->value => ['label' => 'AJK Cabang', 'color' => '#2563eb'],
            CategoryType::Amk->value => ['label' => 'AMK', 'color' => '#059669'],
            CategoryType::Wanita->value => ['label' => 'Wanita', 'color' => '#e11d48'],
        ];

        foreach ($config as $type => $meta) {
            $data = $months->map(function (string $month) use ($rows, $type) {
                return $rows->where('month', $month)->where('category_type', $type)->first()?->total ?? 0;
            })->values()->all();

            $datasets[] = [
                'label' => $meta['label'],
                'data' => $data,
                'backgroundColor' => $meta['color'],
                'borderRadius' => 6,
            ];
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }
}
