<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; }
        body { 
            font-family: 'Arial', sans-serif; 
            color: #333; 
            line-height: 1.6;
        }
        .container { padding: 15px; }
        .header { 
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white; 
            padding: 12px 15px; 
            margin-bottom: 10px;
            border-radius: 6px;
        }
        .header h1 { font-size: 18px; font-weight: bold; margin-bottom: 3px; }
        .header p { font-size: 10px; margin: 1px 0; opacity: 0.95; }
        .section {
            margin-bottom: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
            page-break-inside: avoid;
        }
        .section-title {
            background-color: #f5f5f5;
            padding: 8px 10px;
            font-weight: bold;
            font-size: 11px;
            border-bottom: 2px solid #2563eb;
            color: #333;
        }
        .section-content { padding: 8px 10px; }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .info-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #eee;
            font-size: 10px;
        }
        .info-label {
            font-weight: bold;
            width: 30%;
            background-color: #f5f5f5;
            color: #555;
        }
        .info-value {
            color: #333;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            margin-top: 5px;
            page-break-inside: avoid;
        }
        .data-table thead th {
            background-color: #2563eb;
            color: white;
            padding: 6px 6px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #1e40af;
        }
        .data-table tbody td {
            padding: 5px 6px;
            border: 1px solid #ddd;
            background-color: white;
        }
        .data-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .image-preview {
            text-align: center;
            padding: 8px;
        }
        .image-preview img {
            max-width: 100%;
            max-height: 300px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .note {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 6px 8px;
            margin-top: 5px;
            border-radius: 3px;
            font-size: 9px;
            color: #856404;
        }
        .footer {
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
        .footer p { margin: 1px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ADIKASN - DOKUMEN DIGITAL</h1>
            <p>Anjungan Data Informasi Kepegawaian Aparatur Sipil Negara</p>
            <p>BKPSDM Kabupaten Tabalong</p>
            <p>Dicetak: <?php echo e(now()->format('d M Y H:i')); ?></p>
        </div>

        <div class="section">
            <div class="section-title">📋 INFORMASI FILE</div>
            <div class="section-content">
                <table class="info-table">
                    <tr>
                        <td class="info-label">Nama File</td>
                        <td class="info-value"><strong><?php echo e($file->filename); ?></strong></td>
                    </tr>
                    <tr>
                        <td class="info-label">Tipe File</td>
                        <td class="info-value"><?php echo e(strtoupper($extension)); ?></td>
                    </tr>
                    <tr>
                        <td class="info-label">Satuan Unit Kerja</td>
                        <td class="info-value"><?php echo e($file->skpd?->name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <td class="info-label">Jenis Data</td>
                        <td class="info-value"><?php echo e($file->jenisData?->name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <td class="info-label">Periode</td>
                        <td class="info-value"><?php echo e($file->periode?->name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <td class="info-label">Deskripsi</td>
                        <td class="info-value"><?php echo e($file->description ?? ''); ?></td>
                    </tr>
                    <tr>
                        <td class="info-label">Ukuran File</td>
                        <td class="info-value"><?php echo e($file->getFileSizeFormatted()); ?></td>
                    </tr>
                    <tr>
                        <td class="info-label">Tanggal Upload</td>
                        <td class="info-value"><?php echo e($file->getDateFormatted()); ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <?php if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
            <?php if(file_exists($filePath)): ?>
                <div class="section">
                    <div class="section-title">🖼️ LAMPIRAN VISUAL</div>
                    <div class="section-content image-preview">
                        <?php
                            $finfo = finfo_open(FILEINFO_MIME_TYPE);
                            $mime = finfo_file($finfo, $filePath);
                            finfo_close($finfo);
                        ?>
                        <img src="data:<?php echo e($mime); ?>;base64,<?php echo e(base64_encode(file_get_contents($filePath))); ?>" alt="<?php echo e($file->filename); ?>">
                    </div>
                </div>
            <?php endif; ?>
        <?php elseif($extension === 'xls' || $extension === 'xlsx'): ?>
            <?php if(!empty($tableRows)): ?>
                <div class="section">
                    <div class="section-title">📊 DATA DARI FILE EXCEL</div>
                    <div class="section-content">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <?php $__currentLoopData = $tableHeaders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $header): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th><?php echo e($header); ?></th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $tableRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <?php $__currentLoopData = $row; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cell): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <td><?php echo e($cell); ?></td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <div class="section">
                    <div class="section-title">📊 DATA EXCEL</div>
                    <div class="section-content">
                        <div class="note">
                            ℹ️ File asli berformat .<?php echo e(strtoupper($extension)); ?>. Untuk melihat data lengkap dengan semua isi Excel, silakan unduh file asli dari sistem.
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php elseif($extension === 'pdf'): ?>
            <div class="section">
                <div class="section-title">📄 FILE PDF</div>
                <div class="section-content">
                    <div class="note">
                        ℹ️ File asli berformat PDF. Untuk melihat konten lengkap, silakan unduh file asli.
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="section">
                <div class="section-title">📁 FILE <?php echo e(strtoupper($extension)); ?></div>
                <div class="section-content">
                    <div class="note">
                        ℹ️ File asli berformat .<?php echo e(strtoupper($extension)); ?>. Untuk melihat konten lengkap, silakan unduh file asli dari sistem.
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="footer">
            <p><strong>ADIKASN — BKPSDM Kab. Tabalong</strong></p>
            <p>Dokumen ini diterbitkan secara otomatis oleh sistem pada <?php echo e(now()->format('d M Y H:i')); ?></p>
        </div>
    </div>

    <script type="text/php">
        if ( isset( $pdf ) ) {
            $pdf->set_base_path( base_path() );
        }
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\adikasn666\resources\views/pdf/file-info.blade.php ENDPATH**/ ?>