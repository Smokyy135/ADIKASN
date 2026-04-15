<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'type',
        'name',
    ];

    public $timestamps = true;

    public function uploadedFilesAsSkpd()
    {
        return $this->hasMany(UploadedFile::class, 'skpd_id');
    }

    public function uploadedFilesAsJenisData()
    {
        return $this->hasMany(UploadedFile::class, 'jenis_data_id');
    }

    public function uploadedFilesAsPeriode()
    {
        return $this->hasMany(UploadedFile::class, 'periode_id');
    }
}
