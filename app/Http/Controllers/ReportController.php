<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Report;
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    private const VALID_CATEGORIES = ['matc', 'amk', 'wanita'];

    public function index(): Response
    {
        $this->authorize('viewAny', Report::class);

        $reports = Report::whereIn('category', self::VALID_CATEGORIES)
            ->get()
            ->keyBy('category');

        return Inertia::render('Laporan/Index', [
            'reports' => [
                'matc'   => $reports->get('matc') ? ['original_filename' => $reports->get('matc')->original_filename] : null,
                'amk'    => $reports->get('amk') ? ['original_filename' => $reports->get('amk')->original_filename] : null,
                'wanita' => $reports->get('wanita') ? ['original_filename' => $reports->get('wanita')->original_filename] : null,
            ],
        ]);
    }

    public function upload(Request $request, string $category): RedirectResponse
    {
        $this->authorize('viewAny', Report::class);

        abort_unless(in_array($category, self::VALID_CATEGORIES, true), 404);

        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf', 'max:20480'],
        ]);

        $file = $request->file('file');
        $path = "reports/{$category}.pdf";

        Storage::disk('local')->put($path, file_get_contents($file->getRealPath()));

        Report::updateOrCreate(
            ['category' => $category],
            [
                'original_filename' => $file->getClientOriginalName(),
                'path' => $path,
            ]
        );

        return redirect()->route('laporan.index')->with('success', 'Laporan berjaya dimuat naik.');
    }

    public function downloadFile(string $category): StreamedResponse
    {
        abort_unless(in_array($category, self::VALID_CATEGORIES, true), 404);

        $report = Report::where('category', $category)->firstOrFail();

        abort_unless(Storage::disk('local')->exists($report->path), 404);

        return Storage::disk('local')->download($report->path, $report->original_filename);
    }

    public function downloadQr(string $category): HttpResponse
    {
        $this->authorize('viewAny', Report::class);

        abort_unless(in_array($category, self::VALID_CATEGORIES, true), 404);

        $url = route('laporan.download', $category);

        $jpg = $this->generateQrJpg($url);

        return response($jpg, 200, [
            'Content-Type'        => 'image/jpeg',
            'Content-Disposition' => "attachment; filename=\"qr-laporan-{$category}.jpg\"",
        ]);
    }

    private function generateQrJpg(string $url): string
    {
        $qrCode = Encoder::encode($url, ErrorCorrectionLevel::M());
        $matrix = $qrCode->getMatrix();
        $size   = $matrix->getWidth();

        $scale   = 10;
        $margin  = 4 * $scale;
        $imgSize = $size * $scale + $margin * 2;

        $img   = imagecreatetruecolor($imgSize, $imgSize);
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);

        imagefilledrectangle($img, 0, 0, $imgSize - 1, $imgSize - 1, $white);

        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                if ($matrix->get($x, $y) === 1) {
                    $px = $margin + $x * $scale;
                    $py = $margin + $y * $scale;
                    imagefilledrectangle($img, $px, $py, $px + $scale - 1, $py + $scale - 1, $black);
                }
            }
        }

        ob_start();
        imagejpeg($img, null, 95);
        $jpg = ob_get_clean();
        imagedestroy($img);

        return $jpg;
    }
}
