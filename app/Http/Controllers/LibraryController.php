<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\UploadedFile;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LibraryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = UploadedFile::with('kabupaten', 'skpd', 'jenisData', 'periode', 'uploadedBy');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('filename', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kabupaten')) {
            $query->whereHas('kabupaten', fn ($q) => $q->where('name', $request->kabupaten));
        }
        if ($request->filled('skpd')) {
            $query->whereHas('skpd', fn ($q) => $q->where('name', $request->skpd));
        }
        if ($request->filled('jenis')) {
            $query->whereHas('jenisData', fn ($q) => $q->where('name', $request->jenis));
        }
        if ($request->filled('periode')) {
            $query->whereHas('periode', fn ($q) => $q->where('name', $request->periode));
        }
        if ($request->filled('type')) {
            $query->where('extension', strtolower($request->type));
        }

        $files = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        $categories = Category::get()->groupBy('type');

        return view('library.index', [
            'files' => $files,
            'categories' => $categories,
        ]);
    }

    public function exportPdf(Request $request)
    {
        $query = UploadedFile::with('kabupaten', 'skpd', 'jenisData', 'periode', 'uploadedBy');

        if ($request->filled('kabupaten')) {
            $query->whereHas('kabupaten', fn ($q) => $q->where('name', $request->kabupaten));
        }
        if ($request->filled('skpd')) {
            $query->whereHas('skpd', fn ($q) => $q->where('name', $request->skpd));
        }
        if ($request->filled('jenis')) {
            $query->whereHas('jenisData', fn ($q) => $q->where('name', $request->jenis));
        }
        if ($request->filled('periode')) {
            $query->whereHas('periode', fn ($q) => $q->where('name', $request->periode));
        }

        $files = $query->orderBy('created_at', 'desc')->get();

        $pdf = Pdf::loadView('library.pdf', [
            'files' => $files,
            'filters' => $request->only(['kabupaten', 'skpd', 'jenis', 'periode']),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('ADIKASN-Library-'.date('Y-m-d').'.pdf');
    }

    public function exportExcel(Request $request)
    {
        $query = UploadedFile::with('kabupaten', 'skpd', 'jenisData', 'periode', 'uploadedBy');

        if ($request->filled('kabupaten')) {
            $query->whereHas('kabupaten', fn ($q) => $q->where('name', $request->kabupaten));
        }
        if ($request->filled('skpd')) {
            $query->whereHas('skpd', fn ($q) => $q->where('name', $request->skpd));
        }
        if ($request->filled('jenis')) {
            $query->whereHas('jenisData', fn ($q) => $q->where('name', $request->jenis));
        }
        if ($request->filled('periode')) {
            $query->whereHas('periode', fn ($q) => $q->where('name', $request->periode));
        }

        $files = $query->orderBy('created_at', 'desc')->get();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'LAPORAN DATA FILE ADIKASN');
        $sheet->setCellValue('A2', 'BKPSDM Kabupaten Tabalong');
        $sheet->setCellValue('A3', 'Dicetak: '.date('d M Y H:i'));
        $sheet->mergeCells('A1:K1');
        $sheet->mergeCells('A2:K2');
        $sheet->mergeCells('A3:K3');

        $sheet->getStyle('A1:A3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('A2:A3')->applyFromArray([
            'font' => ['size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $headers = ['No', 'Nama File', 'Kabupaten', 'Satuan Unit Kerja', 'Jenis Data', 'Periode', 'Tipe', 'Ukuran', 'Deskripsi', 'Tanggal Upload', 'Diupload Oleh'];
        $row = 5;

        foreach ($headers as $col => $header) {
            $cell = Cell::stringFromColumnIndex($col).$row;
            $sheet->setCellValue($cell, $header);
        }

        $sheet->getStyle('A5:'.Cell::stringFromColumnIndex(count($headers) - 1).'5')
            ->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563eb']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

        $row = 6;
        $no = 1;
        foreach ($files as $file) {
            $sheet->setCellValue('A'.$row, $no++);
            $sheet->setCellValue('B'.$row, $file->filename);
            $sheet->setCellValue('C'.$row, $file->kabupaten?->name ?? '-');
            $sheet->setCellValue('D'.$row, $file->skpd?->name ?? '-');
            $sheet->setCellValue('E'.$row, $file->jenisData?->name ?? '-');
            $sheet->setCellValue('F'.$row, $file->periode?->name ?? '-');
            $sheet->setCellValue('G'.$row, strtoupper($file->extension));
            $sheet->setCellValue('H'.$row, $file->getFileSizeFormatted());
            $sheet->setCellValue('I'.$row, $file->description ?? '-');
            $sheet->setCellValue('J'.$row, $file->getDateFormatted());
            $sheet->setCellValue('K'.$row, $file->uploadedBy?->name ?? '-');

            if ($row % 2 == 0) {
                $sheet->getStyle('A'.$row.':K'.$row)
                    ->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f8fafc']],
                    ]);
            }

            $sheet->getStyle('A'.$row.':K'.$row)
                ->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);

            $row++;
        }

        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'ADIKASN-Library-'.date('Y-m-d').'.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function preview($id)
    {
        $file = UploadedFile::with('kabupaten', 'skpd', 'jenisData', 'periode', 'uploadedBy')->findOrFail($id);

        $ext = strtolower($file->extension);
        $path = storage_path('app/public/'.$file->filepath);
        $dataUrl = '';

        if (file_exists($path)) {
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $path);
                finfo_close($finfo);
                $data = base64_encode(file_get_contents($path));
                $dataUrl = "data:$mime;base64,$data";
            } elseif ($ext === 'pdf') {
                $dataUrl = url('storage/'.$file->filepath);
            }
        }

        return response()->json([
            'success' => true,
            'file' => [
                'id' => $file->id,
                'filename' => $file->filename,
                'filepath' => url('storage/'.$file->filepath),
                'dataUrl' => $dataUrl,
                'extension' => $ext,
                'size' => $file->getFileSizeFormatted(),
                'kabupaten' => $file->kabupaten?->name,
                'skpd' => $file->skpd?->name,
                'jenis' => $file->jenisData?->name,
                'periode' => $file->periode?->name,
                'description' => $file->description,
                'date' => $file->getDateFormatted(),
                'uploaded_by' => $file->uploadedBy?->name,
            ],
        ]);
    }

    public function download($id)
    {
        $file = UploadedFile::findOrFail($id);
        $path = storage_path('app/public/'.$file->filepath);

        if (! file_exists($path)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->download($path, $file->filename);
    }

    public function printFile($id)
    {
        $file = UploadedFile::with('kabupaten', 'skpd', 'jenisData', 'periode', 'uploadedBy')->findOrFail($id);
        $path = storage_path('app/public/'.$file->filepath);
        $ext = strtolower($file->extension);

        $tableHeaders = [];
        $tableRows = [];
        $excelData = null;

        if (in_array($ext, ['xls', 'xlsx'])) {
            try {
                $spreadsheet = IOFactory::load($path);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = [];

                foreach ($worksheet->getRowIterator() as $rowIndex => $row) {
                    $rowData = [];
                    foreach ($row->getCellIterator() as $cell) {
                        $value = $cell->getCalculatedValue();
                        if ($value === null || $value === '') {
                            $value = $cell->getValue();
                        }
                        if ($value !== null && $value !== '') {
                            $rowData[] = $value;
                        }
                    }
                    if (! empty($rowData)) {
                        $rows[] = $rowData;
                    }
                }

                if (! empty($rows)) {
                    $tableHeaders = $rows[0];
                    $tableRows = array_slice($rows, 1);
                    $excelData = $rows;
                }
            } catch (\Exception $e) {
                \Log::warning('Could not parse Excel file: '.$e->getMessage());
            }
        }

        $pdf = Pdf::loadView('pdf.file-info', [
            'file' => $file,
            'filePath' => $path,
            'extension' => $ext,
            'excelData' => $excelData,
            'tableHeaders' => $tableHeaders,
            'tableRows' => $tableRows,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('ADIKASN-'.$file->id.'.pdf');
    }

    public function exportSingleFilePdf($id)
    {
        return $this->printFile($id);
    }
}
