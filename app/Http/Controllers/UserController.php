<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\UploadedFile;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:user');
    }

    public function dashboard()
    {
        $categories = Category::get()->groupBy('type');
        $files = UploadedFile::with('uploadedBy', 'kabupaten', 'skpd', 'jenisData', 'periode')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.dashboard', [
            'categories' => $categories,
            'files' => $files,
        ]);
    }

    public function getFiles()
    {
        try {
            $files = UploadedFile::with('uploadedBy', 'kabupaten', 'skpd', 'jenisData', 'periode')
                ->orderBy('created_at', 'desc')
                ->get();
            
            \Log::info('User getFiles - Total files:', ['count' => $files->count()]);
            
            $mappedFiles = $files->map(function($f) {
                $data = $this->fileData($f);
                \Log::info('File data for ' . $f->filename, [
                    'kabupaten_id' => $f->kabupaten_id,
                    'skpd_id' => $f->skpd_id,
                    'jenis_data_id' => $f->jenis_data_id,
                    'periode_id' => $f->periode_id,
                    'kabupaten' => $data['kabupaten'],
                    'skpd' => $data['skpd'],
                    'jenis' => $data['jenis'],
                    'periode' => $data['periode'],
                ]);
                return $data;
            });

            return response()->json([
                'success' => true,
                'files' => $mappedFiles,
            ]);
        } catch (\Exception $e) {
            \Log::error('Get files error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat file: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getCategories()
    {
        try {
            $categories = Category::orderBy('name')->get()->groupBy('type');
            
            return response()->json([
                'success' => true,
                'categories' => [
                    'kabupaten' => $categories->get('kabupaten', collect())->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->values(),
                    'skpd' => $categories->get('skpd', collect())->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->values(),
                    'jenisData' => $categories->get('jenis_data', collect())->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->values(),
                    'periode' => $categories->get('periode', collect())->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->values(),
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Get categories error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat kategori: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function filterFiles(Request $request)
    {
        $query = UploadedFile::with('uploadedBy', 'kabupaten', 'skpd', 'jenisData', 'periode');

        if ($request->kabupaten) {
            $query->whereHas('kabupaten', fn($q) => $q->where('name', $request->kabupaten));
        }
        if ($request->skpd) {
            $query->whereHas('skpd', fn($q) => $q->where('name', $request->skpd));
        }
        if ($request->jenis_data) {
            $query->whereHas('jenisData', fn($q) => $q->where('name', $request->jenis_data));
        }
        if ($request->periode) {
            $query->whereHas('periode', fn($q) => $q->where('name', $request->periode));
        }

        $files = $query->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($f) => $this->fileData($f));

        return response()->json([
            'success' => true,
            'files' => $files,
            'count' => $files->count(),
        ]);
    }

    public function downloadFile($id)
    {
        $file = UploadedFile::findOrFail($id);
        $path = storage_path('app/public/' . $file->filepath);
        
        if (!file_exists($path)) {
            return response()->json(['success' => false, 'message' => 'File not found'], 404);
        }

        return response()->download($path, $file->filename);
    }

    public function exportPdf($id)
    {
        try {
            $file = UploadedFile::with('skpd', 'jenisData', 'periode')->findOrFail($id);
            $path = storage_path('app/public/' . $file->filepath);
            $ext = strtolower($file->extension);

            $data = [
                'file' => $file,
                'filePath' => $path,
                'extension' => $ext,
                'excelData' => null,
                'tableHeaders' => [],
                'tableRows' => [],
            ];

            // Parse Excel files
            if (in_array($ext, ['xls', 'xlsx'])) {
                try {
                    $spreadsheet = IOFactory::load($path);
                    $worksheet = $spreadsheet->getActiveSheet();
                    
                    $rows = [];
                    foreach ($worksheet->getRowIterator() as $rowIndex => $row) {
                        $rowData = [];
                        foreach ($row->getCellIterator() as $cell) {
                            // Get calculated value for cells with formulas
                            $value = $cell->getCalculatedValue();
                            
                            // If still null or empty, try to get raw value
                            if ($value === null || $value === '') {
                                $value = $cell->getValue();
                            }
                            
                            if ($value !== null && $value !== '') {
                                $rowData[] = $value;
                            }
                        }
                        if (!empty($rowData)) {
                            $rows[] = $rowData;
                        }
                    }

                    if (!empty($rows)) {
                        $data['tableHeaders'] = $rows[0];
                        $data['tableRows'] = array_slice($rows, 1);
                        $data['excelData'] = $rows;
                    }
                } catch (\Exception $parseError) {
                    \Log::warning('Could not parse Excel file: ' . $parseError->getMessage());
                }
            }

            $pdf = Pdf::loadView('pdf.file-info', $data)
                ->setPaper('a4', 'portrait');

            return $pdf->download('ADIKASN-' . $file->id . '.pdf');
        } catch (\Exception $e) {
            \Log::error('Export PDF error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat PDF: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function fileData($file)
    {
        $path = storage_path('app/public/' . $file->filepath);
        $dataUrl = '';
        
        if (file_exists($path)) {
            $ext = strtolower($file->extension);
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $path);
                finfo_close($finfo);
                $data = base64_encode(file_get_contents($path));
                $dataUrl = "data:$mime;base64,$data";
            } elseif ($ext === 'pdf') {
                $dataUrl = url('storage/' . $file->filepath);
            }
        }

        return [
            'id' => $file->id,
            'filename' => $file->filename,
            'kabupaten' => $file->kabupaten?->name,
            'filepath' => url('storage/' . $file->filepath),
            'dataUrl' => $dataUrl,
            'size' => $file->filesize,
            'ext' => strtolower($file->extension),
            'skpd' => $file->skpd?->name,
            'jenis' => $file->jenisData?->name,
            'periode' => $file->periode?->name,
            'desc' => $file->description,
            'date' => $file->created_at->format('d M Y'),
            'timestamp' => $file->created_at->toIso8601String(),
        ];
    }

    public function exportFilteredDataPdf(Request $request)
    {
        try {
            $query = UploadedFile::with('kabupaten', 'skpd', 'jenisData', 'periode', 'uploadedBy');

            if ($request->filled('kabupaten')) {
                $query->whereHas('kabupaten', fn($q) => $q->where('name', $request->kabupaten));
            }
            if ($request->filled('skpd')) {
                $query->whereHas('skpd', fn($q) => $q->where('name', $request->skpd));
            }
            if ($request->filled('jenis_data')) {
                $query->whereHas('jenisData', fn($q) => $q->where('name', $request->jenis_data));
            }
            if ($request->filled('periode')) {
                $query->whereHas('periode', fn($q) => $q->where('name', $request->periode));
            }

            $files = $query->orderBy('created_at', 'desc')->get();

            $pdf = Pdf::loadView('user.export-pdf', [
                'files' => $files,
                'filters' => $request->only(['kabupaten', 'skpd', 'jenis_data', 'periode']),
                'exportDate' => now()->format('d M Y H:i'),
                'userRole' => 'User',
            ])->setPaper('a4', 'landscape');

            return $pdf->download('ADIKASN-Data-' . date('Y-m-d-His') . '.pdf');
        } catch (\Exception $e) {
            \Log::error('Export PDF error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat PDF: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function exportFilteredDataExcel(Request $request)
    {
        try {
            $query = UploadedFile::with('kabupaten', 'skpd', 'jenisData', 'periode', 'uploadedBy');

            if ($request->filled('kabupaten')) {
                $query->whereHas('kabupaten', fn($q) => $q->where('name', $request->kabupaten));
            }
            if ($request->filled('skpd')) {
                $query->whereHas('skpd', fn($q) => $q->where('name', $request->skpd));
            }
            if ($request->filled('jenis_data')) {
                $query->whereHas('jenisData', fn($q) => $q->where('name', $request->jenis_data));
            }
            if ($request->filled('periode')) {
                $query->whereHas('periode', fn($q) => $q->where('name', $request->periode));
            }

            $files = $query->orderBy('created_at', 'desc')->get();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Data Files');

            // Set column headers
            $headers = ['No', 'Nama File', 'Kabupaten', 'SKPD', 'Jenis Data', 'Periode', 'Ukuran', 'Upload Oleh', 'Tanggal Upload'];
            $colIndex = 'A';
            
            foreach ($headers as $header) {
                $sheet->setCellValue($colIndex . '1', $header);
                $sheet->getStyle($colIndex . '1')->getFont()->setBold(true);
                $sheet->getStyle($colIndex . '1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                $sheet->getStyle($colIndex . '1')->getFill()->getStartColor()->setARGB('FF3B82F6');
                $sheet->getStyle($colIndex . '1')->getFont()->getColor()->setARGB('FFFFFFFF');
                $colIndex++;
            }

            // Add data rows
            $row = 2;
            foreach ($files as $index => $file) {
                $sheet->setCellValue('A' . $row, $index + 1);
                $sheet->setCellValue('B' . $row, $file->filename);
                $sheet->setCellValue('C' . $row, $file->kabupaten?->name ?? '-');
                $sheet->setCellValue('D' . $row, $file->skpd?->name ?? '-');
                $sheet->setCellValue('E' . $row, $file->jenisData?->name ?? '-');
                $sheet->setCellValue('F' . $row, $file->periode?->name ?? '-');
                $sheet->setCellValue('G' . $row, $this->formatFileSize($file->filesize));
                $sheet->setCellValue('H' . $row, $file->uploadedBy?->name ?? '-');
                $sheet->setCellValue('I' . $row, $file->created_at->format('d M Y H:i'));
                $row++;
            }

            // Auto-fit columns
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(25);
            $sheet->getColumnDimension('E')->setWidth(20);
            $sheet->getColumnDimension('F')->setWidth(15);
            $sheet->getColumnDimension('G')->setWidth(12);
            $sheet->getColumnDimension('H')->setWidth(20);
            $sheet->getColumnDimension('I')->setWidth(18);

            // Center alignment for number column
            $sheet->getStyle('A2:A' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Write file
            $writer = new Xlsx($spreadsheet);
            $fileName = 'ADIKASN-Data-' . date('Y-m-d-His') . '.xlsx';
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $fileName . '"');
            header('Cache-Control: max-age=0');
            
            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            \Log::error('Export Excel error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat Excel: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
