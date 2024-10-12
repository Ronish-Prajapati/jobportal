<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable(); // Optional title for the section
            $table->text('content')->nullable(); // Can hold HTML or JSON for the content
            $table->integer('order')->default(0); // Controls the order of sections on the page
            $table->unsignedBigInteger('template_id')->nullable(); // Optional template for this section
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
