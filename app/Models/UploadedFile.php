<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadedFile extends Model
{
    protected $fillable = [
        'filename',
        'filepath',
        'filesize',
        'extension',
        'mime_type',
        'description',
        'kabupaten_id',
        'skpd_id',
        'jenis_data_id',
        'periode_id',
        'uploaded_by',
    ];

    public $timestamps = true;

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function kabupaten()
    {
        return $this->belongsTo(Category::class, 'kabupaten_id');
    }

    public function skpd()
    {
        return $this->belongsTo(Category::class, 'skpd_id');
    }

    public function jenisData()
    {
        return $this->belongsTo(Category::class, 'jenis_data_id');
    }

    public function periode()
    {
        return $this->belongsTo(Category::class, 'periode_id');
    }

    public function getFileSizeFormatted()
    {
        $bytes = $this->filesize;
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $bytes;
        $unit = 0;
        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }
        return round($size, 1) . ' ' . $units[$unit];
    }

    public function getDateFormatted()
    {
        return $this->created_at->format('d M Y');
    }
}
