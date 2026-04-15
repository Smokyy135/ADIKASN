<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('uploaded_files', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('filepath');
            $table->bigInteger('filesize');
            $table->string('extension');
            $table->string('mime_type');
            $table->text('description')->nullable();
            $table->foreignId('kabupaten_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->foreignId('skpd_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->foreignId('jenis_data_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->foreignId('periode_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->index(['kabupaten_id', 'skpd_id', 'jenis_data_id', 'periode_id'], 'idx_file_categories');
            $table->index('uploaded_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uploaded_files');
    }
};
