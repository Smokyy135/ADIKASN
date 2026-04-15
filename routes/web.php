<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::prefix('admin')->middleware('auth', 'role:admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // File Management
    Route::post('/upload', [AdminController::class, 'uploadFile'])->name('upload');
    Route::delete('/file/{id}', [AdminController::class, 'deleteFile'])->name('file.delete');
    Route::delete('/files/clear-all', [AdminController::class, 'deleteAllFiles'])->name('files.clear');
    Route::get('/files', [AdminController::class, 'getFiles'])->name('files');
    
    // Category Management
    Route::get('/categories', [AdminController::class, 'getCategories'])->name('categories');
    Route::post('/category', [AdminController::class, 'addCategory'])->name('category.add');
    Route::delete('/category/{id}', [AdminController::class, 'deleteCategory'])->name('category.delete');
    Route::post('/category/delete-by-name', [AdminController::class, 'deleteCategoryByName'])->name('category.delete-by-name');
});

Route::prefix('user')->middleware('auth', 'role:user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    
    // File Viewing & Download
    Route::get('/files', [UserController::class, 'getFiles'])->name('files');
    Route::get('/categories', [UserController::class, 'getCategories'])->name('categories');
    Route::post('/filter', [UserController::class, 'filterFiles'])->name('filter');
    Route::get('/file/{id}/download', [UserController::class, 'downloadFile'])->name('file.download');
    Route::get('/file/{id}/export-pdf', [UserController::class, 'exportPdf'])->name('file.export-pdf');
    
    // Bulk Export
    Route::post('/export-pdf', [UserController::class, 'exportFilteredDataPdf'])->name('export.pdf');
    Route::post('/export-excel', [UserController::class, 'exportFilteredDataExcel'])->name('export.excel');
});

Route::get('/', function () {
    return Auth::check()
        ? redirect(Auth::user()->role === 'admin' ? '/admin/dashboard' : '/user/dashboard')
        : redirect('/login');
});

// Debug route - only in local environment
if (env('APP_ENV') === 'local') {
    Route::get('/debug', function () {
        $files = \App\Models\UploadedFile::with('kabupaten', 'skpd', 'jenisData', 'periode', 'uploadedBy')->get();
        $categories = \App\Models\Category::all();
        
        return response()->json([
            'categories' => [
                'count' => $categories->count(),
                'items' => $categories->map(fn($c) => ['id' => $c->id, 'type' => $c->type, 'name' => $c->name])->toArray()
            ],
            'files' => [
                'count' => $files->count(),
                'items' => $files->map(fn($f) => [
                    'id' => $f->id,
                    'filename' => $f->filename,
                    'kabupaten_id' => $f->kabupaten_id,
                    'kabupaten_name' => $f->kabupaten?->name,
                    'skpd_id' => $f->skpd_id,
                    'skpd_name' => $f->skpd?->name,
                    'jenis_data_id' => $f->jenis_data_id,
                    'jenis_data_name' => $f->jenisData?->name,
                    'periode_id' => $f->periode_id,
                    'periode_name' => $f->periode?->name,
                    'uploaded_by' => $f->uploaded_by,
                    'uploaded_by_name' => $f->uploadedBy?->name,
                ])->toArray()
            ]
        ]);
    });
}
