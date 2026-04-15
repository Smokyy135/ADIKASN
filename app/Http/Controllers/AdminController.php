<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function dashboard()
    {
        $categories = Category::get()->groupBy('type');
        $files = UploadedFile::with('uploadedBy', 'skpd', 'jenisData', 'periode')->orderBy('created_at', 'desc')->get();
        
        return view('admin.dashboard', [
            'categories' => $categories,
            'files' => $files,
        ]);
    }

    public function uploadFile(Request $request)
    {
        try {
            \Log::info('=== UPLOAD FILE REQUEST ===');
            \Log::info('Request data:', [
                'kabupaten_id' => $request->kabupaten_id,
                'skpd_id' => $request->skpd_id,
                'jenis_data_id' => $request->jenis_data_id,
                'periode_id' => $request->periode_id,
                'file_name' => $request->file('file')?->getClientOriginalName(),
            ]);
            
            // Allowed MIME types: PDF, Office documents (Word, Excel), Images
            $allowedMimes = 'pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif,webp';
            $request->validate([
                'file' => "required|file|max:5120|mimes:$allowedMimes",
                'kabupaten_id' => 'nullable|exists:categories,id',
                'skpd_id' => 'nullable|exists:categories,id',
                'jenis_data_id' => 'nullable|exists:categories,id',
                'periode_id' => 'nullable|exists:categories,id',
                'description' => 'nullable|string|max:255',
            ], [
                'file.mimes' => 'File harus berupa: PDF, Word (DOC/DOCX), Excel (XLS/XLSX), atau Gambar (JPG/PNG/GIF/WEBP)',
                'file.max' => 'File tidak boleh lebih dari 5MB',
            ]);

            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $mimeType = $file->getClientMimeType();
            $filesize = $file->getSize();

            $filepath = $file->store('uploads', 'public');

            $uploadedFile = UploadedFile::create([
                'filename' => $filename,
                'filepath' => $filepath,
                'filesize' => $filesize,
                'extension' => $extension,
                'mime_type' => $mimeType,
                'description' => $request->description,
                'kabupaten_id' => $request->kabupaten_id,
                'skpd_id' => $request->skpd_id,
                'jenis_data_id' => $request->jenis_data_id,
                'periode_id' => $request->periode_id,
                'uploaded_by' => Auth::id(),
            ]);
            
            \Log::info('File uploaded successfully:', [
                'id' => $uploadedFile->id,
                'filename' => $uploadedFile->filename,
                'kabupaten_id' => $uploadedFile->kabupaten_id,
                'skpd_id' => $uploadedFile->skpd_id,
                'jenis_data_id' => $uploadedFile->jenis_data_id,
                'periode_id' => $uploadedFile->periode_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => "\"$filename\" berhasil dipublikasikan!",
                'file' => $this->fileData($uploadedFile),
            ]);
        } catch (\Exception $e) {
            \Log::error('Upload file error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload file: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function deleteFile($id)
    {
        $file = UploadedFile::findOrFail($id);
        
        if ($file->uploaded_by !== Auth::id() && !Auth::user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        Storage::disk('public')->delete($file->filepath);
        $filename = $file->filename;
        $file->delete();

        return response()->json([
            'success' => true,
            'message' => "\"$filename\" berhasil dihapus!",
        ]);
    }

    public function deleteAllFiles()
    {
        $files = UploadedFile::all();
        foreach ($files as $file) {
            Storage::disk('public')->delete($file->filepath);
            $file->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Semua file berhasil dihapus!',
        ]);
    }

    public function addCategory(Request $request)
    {
        try {
            $validTypes = ['kabupaten', 'skpd', 'jenis_data', 'periode'];
            
            $validated = $request->validate([
                'type' => 'required|string|in:kabupaten,skpd,jenis_data,periode',
                'name' => 'required|string|max:255|min:1',
            ], [
                'type.in' => 'Tipe kategori harus salah satu dari: kabupaten, skpd, jenis_data, periode',
                'name.required' => 'Nama kategori harus diisi',
                'name.max' => 'Nama kategori maksimal 255 karakter',
            ]);

            // Validate type value
            if (!in_array($validated['type'], $validTypes)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipe kategori tidak valid: ' . $validated['type'],
                ], 422);
            }

            // Check if already exists
            $exists = Category::where('type', $validated['type'])
                ->where('name', $validated['name'])
                ->first();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori "' . $validated['name'] . '" sudah ada untuk tipe "' . $validated['type'] . '"!',
                ], 409);
            }

            // Create new category
            $category = Category::create([
                'type' => $validated['type'],
                'name' => $validated['name'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kategori "' . $validated['name'] . '" berhasil ditambahkan!',
                'categories' => $this->getCategoriesData(),
                'new_category' => [
                    'id' => $category->id,
                    'type' => $category->type,
                    'name' => $category->name,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->errors();
            $errorMsg = '';
            if (isset($errors['type'])) {
                $errorMsg = $errors['type'][0];
            } elseif (isset($errors['name'])) {
                $errorMsg = $errors['name'][0];
            } else {
                $errorMsg = 'Validasi gagal';
            }
            
            return response()->json([
                'success' => false,
                'message' => $errorMsg,
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Add category error: ' . $e->getMessage(), ['exception' => $e]);
            
            // Parse database-specific errors
            $errorMsg = $e->getMessage();
            if (strpos($errorMsg, 'SQLSTATE') !== false) {
                if (strpos($errorMsg, 'CHECK constraint failed') !== false) {
                    $errorMsg = 'Tipe kategori tidak valid. Gunakan: kabupaten, skpd, jenis_data, atau periode.';
                } elseif (strpos($errorMsg, 'UNIQUE constraint failed') !== false) {
                    $errorMsg = 'Kategori ini sudah ada di database.';
                } else {
                    $errorMsg = 'Error database: ' . substr($errorMsg, 0, 100);
                }
            }
            
            return response()->json([
                'success' => false,
                'message' => $errorMsg,
            ], 500);
        }
    }

    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        $name = $category->name;
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => "\"$name\" berhasil dihapus!",
            'categories' => $this->getCategoriesData(),
        ]);
    }

    public function deleteCategoryByName(Request $request)
    {
        $request->validate([
            'type' => 'required|in:kabupaten,skpd,jenis_data,periode',
            'name' => 'required|string',
        ]);

        $category = Category::where('type', $request->type)
            ->where('name', $request->name)
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan!',
            ], 404);
        }

        $name = $category->name;
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => "\"$name\" berhasil dihapus!",
            'categories' => $this->getCategoriesData(),
        ]);
    }

    public function getCategories()
    {
        return response()->json([
            'success' => true,
            'categories' => $this->getCategoriesData(),
        ]);
    }

    public function getFiles()
    {
        $files = UploadedFile::with('uploadedBy', 'kabupaten', 'skpd', 'jenisData', 'periode')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($f) => $this->fileData($f));

        return response()->json([
            'success' => true,
            'files' => $files,
        ]);
    }

    private function getCategoriesData()
    {
        $categories = Category::orderBy('name')->get()->groupBy('type');
        
        return [
            'kabupaten' => $categories->get('kabupaten', collect())->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->values(),
            'skpd' => $categories->get('skpd', collect())->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->values(),
            'jenisData' => $categories->get('jenis_data', collect())->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->values(),
            'periode' => $categories->get('periode', collect())->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->values(),
        ];
    }

    private function fileData($file)
    {
        $path = storage_path('app/public/' . $file->filepath);
        $dataUrl = '';
        
        if (file_exists($path)) {
            $ext = strtolower($file->extension);
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                // Use finfo instead of deprecated mime_content_type
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
            'filepath' => url('storage/' . $file->filepath),
            'dataUrl' => $dataUrl,
            'size' => $file->filesize,
            'kabupaten' => $file->kabupaten?->name,
            'ext' => strtolower($file->extension),
            'skpd' => $file->skpd?->name,
            'jenis' => $file->jenisData?->name,
            'periode' => $file->periode?->name,
            'desc' => $file->description,
            'date' => $file->created_at->format('d M Y'),
            'timestamp' => $file->created_at->toIso8601String(),
        ];
    }
}
