<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            color: #1f2937;
            background: white;
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
        .info-section {
            margin-bottom: 25px;
            padding: 15px;
            background-color: #f9fafb;
            border-left: 3px solid #2563eb;
            border-radius: 4px;
        }
        .info-section-title {
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 12px;
            font-size: 12px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 6px 0;
            font-size: 11px;
        }
        .info-label {
            font-weight: 600;
            color: #374151;
            min-width: 120px;
        }
        .info-value {
            color: #6b7280;
            text-align: right;
            flex: 1;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 25px;
        }
        .data-table thead th {
            background-color: #2563eb;
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
        .image-preview {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background-color: #f9fafb;
            border-radius: 4px;
        }
        .image-preview img {
            max-width: 100%;
            max-height: 400px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
        }
        .footer {
            text-align: right;
            font-size: 10px;
            color: #6b7280;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="title">DOKUMEN FILE ADIKASN</div>
            <div class="subtitle">BADAN KEPEGAWAIAN DAN PENGEMBANGAN SUMBER DAYA MANUSIA</div>
            <div class="report-period">KABUPATEN TABALONG</div>
        </div>

        <!-- File Information -->
        <div class="info-section">
            <div class="info-section-title">INFORMASI FILE</div>
            <div class="info-row">
                <span class="info-label">Nama File:</span>
                <span class="info-value"><strong><?php echo e($file->filename); ?></strong></span>
            </div>
            <div class="info-row">
                <span class="info-label">Tipe File:</span>
                <span class="info-value"><?php echo e(strtoupper($extension)); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Unit Kerja (SKPD):</span>
                <span class="info-value"><?php echo e($file->skpd?->name ?? '-'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Jenis Data:</span>
                <span class="info-value"><?php echo e($file->jenisData?->name ?? '-'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Periode:</span>
                <span class="info-value"><?php echo e($file->periode?->name ?? '-'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Ukuran File:</span>
                <span class="info-value"><?php echo e($file->getFileSizeFormatted()); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Upload Pada:</span>
                <span class="info-value"><?php echo e($file->created_at->format('d-m-Y H:i')); ?></span>
            </div>
        </div>

        <!-- Excel Data Table (jika ada) -->
        <?php if($extension === 'xlsx' || $extension === 'xls'): ?>
            <?php if(!empty($tableRows)): ?>
            <div style="margin-bottom: 25px;">
                <h3 style="font-size: 12px; font-weight: 700; color: #1f2937; margin-bottom: 12px;">PREVIEW DATA</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <?php $__currentLoopData = $tableHeaders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $header): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th><?php echo e($header); ?></th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $tableRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <?php $__currentLoopData = $row; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cell): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <td><?php echo e($cell); ?></td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="<?php echo e(count($tableHeaders)); ?>" style="text-align: center; padding: 15px;">Tidak ada data</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Image Preview -->
        <?php if($extension === 'jpg' || $extension === 'jpeg' || $extension === 'png' || $extension === 'gif' || $extension === 'webp'): ?>
            <?php if(file_exists($filePath)): ?>
            <div class="image-preview">
                <h3 style="font-size: 12px; font-weight: 700; color: #1f2937; margin-bottom: 12px;">PREVIEW GAMBAR</h3>
                <img src="data:image/<?php echo e($extension); ?>;base64,<?php echo e(base64_encode(file_get_contents($filePath))); ?>" alt="<?php echo e($file->filename); ?>">
            </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Footer -->
        <div class="footer">
            Dicetak pada: <?php echo e(now()->format('d-m-Y H:i:s')); ?>

        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\user\Videos\adikasn666\resources\views/pdf/file-info.blade.php ENDPATH**/ ?>