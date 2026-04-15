<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            color: #1f2937;
            background: white;
            padding: 0;
        }
        .container { 
            padding: 40px 35px;
            max-width: 1000px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
        }
        .title {
            font-size: 18px;
            font-weight: 700;
            color: #2563eb;
            margin-bottom: 10px;
            line-height: 1.4;
        }
        .subtitle {
            font-size: 14px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
        }
        .report-period {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 0;
        }
        .table-wrapper {
            margin-bottom: 30px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        .data-table thead tr {
            background-color: #2563eb;
        }
        .data-table thead th {
            color: white;
            padding: 12px 10px;
            text-align: left;
            font-weight: 700;
            border: 1px solid #2563eb;
        }
        .data-table tbody td {
            padding: 10px;
            border: 1px solid #e5e7eb;
            background-color: white;
        }
        .data-table tbody tr:nth-child(even) td {
            background-color: #f3f4f6;
        }
        .data-table tbody tr:nth-child(odd) td {
            background-color: white;
        }
        .no-data {
            text-align: center;
            padding: 20px !important;
            color: #6b7280;
        }
        .footer {
            text-align: right;
            font-size: 10px;
            color: #6b7280;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
        }
        .info-section {
            margin-bottom: 20px;
            font-size: 11px;
        }
        .info-label {
            font-weight: 600;
            color: #374151;
            display: inline-block;
            width: 120px;
        }
        .info-value {
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="title">DATA LIBRARY FILE ADIKASN</div>
            <div class="subtitle">BADAN KEPEGAWAIAN DAN PENGEMBANGAN SUMBER DAYA MANUSIA</div>
            <div class="report-period">KABUPATEN TABALONG</div>
        </div>

        <!-- Filter Information -->
        @if($filters['kabupaten'] || $filters['skpd'] || $filters['jenis_data'] || $filters['periode'])
        <div class="info-section" style="margin-bottom: 25px;">
            <div style="font-weight: 700; color: #1f2937; margin-bottom: 8px;">FILTER LAPORAN:</div>
            @if($filters['kabupaten'])
            <div><span class="info-label">Kabupaten:</span><span class="info-value">{{ $filters['kabupaten'] }}</span></div>
            @endif
            @if($filters['skpd'])
            <div><span class="info-label">SKPD:</span><span class="info-value">{{ $filters['skpd'] }}</span></div>
            @endif
            @if($filters['jenis_data'])
            <div><span class="info-label">Jenis Data:</span><span class="info-value">{{ $filters['jenis_data'] }}</span></div>
            @endif
            @if($filters['periode'])
            <div><span class="info-label">Periode:</span><span class="info-value">{{ $filters['periode'] }}</span></div>
            @endif
        </div>
        @endif

        <!-- Data Table -->
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 30%;">Nama File</th>
                        <th style="width: 12%;">Kabupaten</th>
                        <th style="width: 15%;">Unit Kerja</th>
                        <th style="width: 12%;">Jenis Data</th>
                        <th style="width: 10%;">Periode</th>
                        <th style="width: 8%;">Ukuran</th>
                        <th style="width: 8%;">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($files as $index => $file)
                    <tr>
                        <td style="text-align: center; font-weight: 500;">{{ $index + 1 }}</td>
                        <td>{{ $file->filename }}</td>
                        <td>{{ $file->kabupaten?->name ?? '-' }}</td>
                        <td>{{ $file->skpd?->name ?? '-' }}</td>
                        <td>{{ $file->jenisData?->name ?? '-' }}</td>
                        <td>{{ $file->periode?->name ?? '-' }}</td>
                        <td style="text-align: center;">{{ $this->formatFileSize($file->filesize) }}</td>
                        <td>{{ $file->created_at->format('d-m-Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="no-data">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            Dicetak pada: {{ now()->format('d F Y H:i:s') }}
        </div>
    </div>
</body>
</html>
