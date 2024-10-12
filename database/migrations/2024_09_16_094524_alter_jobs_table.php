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
    // Use Schema::table to alter the existing 'jobs' table
    Schema::table('jobs', function (Blueprint $table) {
        // Add the 'status' column after 'company_website'
        $table->integer('status')->default(1)->after('company_website');
        
        // Add the 'isFeatured' column after 'status'
        $table->integer('isFeatured')->default(0)->after('status');
    });
}

/**
 * Reverse the migrations.
 */
public function down(): void
{
    // Use Schema::table to remove the columns when rolling back
    Schema::table('jobs', function (Blueprint $table) {
        $table->dropColumn('status');
        $table->dropColumn('isFeatured');
    });
}

};
