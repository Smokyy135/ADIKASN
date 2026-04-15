<?php
require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<pre style='font-size: 11px; font-family: monospace; background: #f4f4f4; padding: 10px;'>";

echo "=== DATABASE DIAGNOSTIC ===\n\n";

// Check categories
echo "1. CATEGORIES IN DATABASE:\n";
$categories = \App\Models\Category::orderBy('type')->get();
echo "Total: " . $categories->count() . "\n";
foreach ($categories as $c) {
    echo "  [{$c->id}] Type: {$c->type}, Name: {$c->name}\n";
}

// Check uploaded files
echo "\n2. UPLOADED FILES IN DATABASE:\n";
$files = \App\Models\UploadedFile::with('kabupaten', 'skpd', 'jenisData', 'periode', 'uploadedBy')->get();
echo "Total: " . $files->count() . "\n";

if ($files->count() === 0) {
    echo "❌ NO FILES IN DATABASE!\n";
} else {
    foreach ($files as $f) {
        echo "\n  📄 {$f->filename}\n";
        echo "     - ID: {$f->id}\n";
        echo "     - kabupaten_id: " . ($f->kabupaten_id ?? 'NULL') . " => " . ($f->kabupaten?->name ?? '❌ NULL') . "\n";
        echo "     - skpd_id: " . ($f->skpd_id ?? 'NULL') . " => " . ($f->skpd?->name ?? 'NULL') . "\n";
        echo "     - jenis_data_id: " . ($f->jenis_data_id ?? 'NULL') . " => " . ($f->jenisData?->name ?? 'NULL') . "\n";
        echo "     - periode_id: " . ($f->periode_id ?? 'NULL') . " => " . ($f->periode?->name ?? 'NULL') . "\n";
        echo "     - uploaded_by: " . $f->uploaded_by . " (" . ($f->uploadedBy?->name ?? 'Unknown') . ")\n";
    }
}

// Simulate API response
echo "\n3. SIMULATING API RESPONSE (getFiles):\n";
$apiResponse = [
    'success' => true,
    'files' => $files->map(fn($f) => [
        'id' => $f->id,
        'filename' => $f->filename,
        'kabupaten' => $f->kabupaten?->name,
        'skpd' => $f->skpd?->name,
        'jenis' => $f->jenisData?->name,
        'periode' => $f->periode?->name,
    ])->toArray()
];

echo json_encode($apiResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";

echo "\n4. DATABASE RAW QUERY:\n";
echo "SELECT * FROM uploaded_files;\n";
$rawFiles = \Illuminate\Support\Facades\DB::table('uploaded_files')->get()->toArray();
foreach ($rawFiles as $rf) {
    echo "  File: {$rf->filename}\n";
    echo "    kabupaten_id={$rf->kabupaten_id}, skpd_id={$rf->skpd_id}, jenis_data_id={$rf->jenis_data_id}, periode_id={$rf->periode_id}\n";
}

echo "\n</pre>";
