<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReferralController extends Controller
{
    /**
     * Common query for listing and exporting applications.
     */
    private function applicationQuery(array $filters)
    {
        $search = trim($filters['search'] ?? '');
        $fromDate = $filters['from_date'] ?? '';
        $toDate = $filters['to_date'] ?? '';

        return Application::query()
            ->when($search !== '', function ($query) use ($search) {
                $like = '%' . $search . '%';

                $query->where(function ($subQuery) use ($like) {
                    $subQuery
                        ->where('full_name', 'like', $like)
                        ->orWhere('mobile_number', 'like', $like)
                        ->orWhere('whatsapp_number', 'like', $like)
                        ->orWhere('referral_name', 'like', $like)
                        ->orWhere('referral_mobile', 'like', $like)
                        ->orWhere('profession', 'like', $like)
                        ->orWhere('primary_interest', 'like', $like);
                });
            })
            ->when($fromDate !== '', function ($query) use ($fromDate) {
                $query->whereDate('created_at', '>=', $fromDate);
            })
            ->when($toDate !== '', function ($query) use ($toDate) {
                $query->whereDate('created_at', '<=', $toDate);
            });
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'from_date' => ['nullable', 'date_format:Y-m-d'],
            'to_date' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:from_date',
            ],
        ]);

        $search = trim($validated['search'] ?? '');
        $fromDate = $validated['from_date'] ?? '';
        $toDate = $validated['to_date'] ?? '';

        $applications = $this->applicationQuery($validated)
            ->latest('created_at')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.referrals.index', compact(
            'applications',
            'search',
            'fromDate',
            'toDate'
        ));
    }

    /**
     * Export filtered or all applications as a formatted Excel file.
     */
    public function export(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'from_date' => ['nullable', 'date_format:Y-m-d'],
            'to_date' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:from_date',
            ],
        ]);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Referrals');

        $headings = [
            'ID',
            'Full Name',
            'Mobile Number',
            'WhatsApp Number',
            'Referral Name',
            'Referral Mobile',
            'Profession',
            'Primary Interest',
            'Created At',
        ];

        $sheet->fromArray($headings, null, 'A1');

        // Style the header row.
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);
        $sheet->getStyle('A1:I1')->getFont()->setSize(11);
        $sheet->getStyle('A1:I1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E7FF');

        $sheet->getStyle('A1:I1')->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $row = 2;

        $this->applicationQuery($validated)
            ->latest('created_at')
            ->latest('id')
            ->chunk(500, function ($applications) use ($sheet, &$row) {
                foreach ($applications as $application) {
                    $sheet->setCellValue('A' . $row, $application->id);
                    $sheet->setCellValue('B' . $row, $application->full_name ?? '');

                    // Store phone numbers as text, not numeric values.
                    $sheet->setCellValueExplicit(
                        'C' . $row,
                        (string) ($application->mobile_number ?? ''),
                        DataType::TYPE_STRING
                    );

                    $sheet->setCellValueExplicit(
                        'D' . $row,
                        (string) ($application->whatsapp_number ?? ''),
                        DataType::TYPE_STRING
                    );

                    $sheet->setCellValue('E' . $row, $application->referral_name ?? '');

                    $sheet->setCellValueExplicit(
                        'F' . $row,
                        (string) ($application->referral_mobile ?? ''),
                        DataType::TYPE_STRING
                    );

                    $sheet->setCellValue('G' . $row, $application->profession ?? '');
                    $sheet->setCellValue('H' . $row, $application->primary_interest ?? '');

                    $sheet->setCellValue(
                        'I' . $row,
                        $application->created_at
                            ? $application->created_at->format('d-m-Y h:i A')
                            : ''
                    );

                    $row++;
                }
            });

        // Automatically size columns to their content.
        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Avoid excessively wide columns.
        foreach (['B', 'E', 'G', 'H'] as $column) {
            $sheet->getColumnDimension($column)->setWidth(28);
        }

        $sheet->getColumnDimension('I')->setWidth(23);
        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:I' . max(1, $row - 1));

        $filename = 'referrals_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return response()->streamDownload(
            function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                $spreadsheet->disconnectWorksheets();
            },
            $filename,
            [
                'Content-Type' =>
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
            ]
        );
    }
}
