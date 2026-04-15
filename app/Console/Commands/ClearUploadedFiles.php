<?php

namespace App\Console\Commands;

use App\Models\UploadedFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ClearUploadedFiles extends Command
{
    protected $signature = 'app:clear-files';
    protected $description = 'Clear all uploaded files from database and storage';

    public function handle()
    {
        $files = UploadedFile::all();
        
        foreach ($files as $file) {
            // Delete from storage
            Storage::disk('public')->delete($file->filepath);
            
            // Delete from database
            $file->delete();
            
            $this->info("Deleted: {$file->filename}");
        }
        
        $this->info('All files cleared successfully!');
        return 0;
    }
}
