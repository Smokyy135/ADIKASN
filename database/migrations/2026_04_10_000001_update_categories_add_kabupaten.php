<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SQLite limitations: Cannot modify enum column directly
        // So we need to recreate the table with new enum values
        
        if (DB::getDriverName() === 'sqlite') {
            // SQLite doesn't support ALTER COLUMN, so we recreate table
            DB::statement('
                CREATE TABLE categories_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    type VARCHAR(255) NOT NULL,
                    name VARCHAR(255) NOT NULL,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL,
                    UNIQUE(type, name)
                );
            ');
            
            // Copy old data
            DB::statement('INSERT INTO categories_new SELECT * FROM categories;');
            
            // Drop old table
            DB::statement('DROP TABLE categories;');
            
            // Rename new table
            DB::statement('ALTER TABLE categories_new RENAME TO categories;');
            
            // Recreate index
            DB::statement('CREATE INDEX categories_type_index ON categories(type);');
        } else {
            // For MySQL/PostgreSQL, modify column type from ENUM to VARCHAR
            Schema::table('categories', function (Blueprint $table) {
                $table->string('type')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to old structure if needed
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('
                CREATE TABLE categories_old (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    type VARCHAR(255) NOT NULL,
                    name VARCHAR(255) NOT NULL,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL,
                    UNIQUE(type, name)
                );
            ');
            
            DB::statement('INSERT INTO categories_old SELECT * FROM categories;');
            DB::statement('DROP TABLE categories;');
            DB::statement('ALTER TABLE categories_old RENAME TO categories;');
            DB::statement('CREATE INDEX categories_type_index ON categories(type);');
        }
    }
};
