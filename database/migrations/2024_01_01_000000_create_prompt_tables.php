<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('prompt_version', function (Blueprint $table) {
            $table->id();
            $table->text('prompt_name');
            $table->text('version_label');
            $table->text('status');
            $table->text('notes')->nullable();
            $table->timestampsTz();
        });

        Schema::create('prompt_section', function (Blueprint $table) {
            $table->id();
            $table->text('key')->unique();
            $table->text('title')->nullable();
            $table->text('description')->nullable();
            $table->integer('order_index')->default(0);
            $table->boolean('enabled')->default(true);
            $table->timestampsTz();
        });

        Schema::create('prompt_line', function (Blueprint $table) {
            $table->id();
            $table->foreignId('version_id')->constrained('prompt_version')->cascadeOnDelete();
            $table->foreignId('section_id')->constrained('prompt_section')->restrictOnDelete();
            $table->integer('order_index')->default(0);
            $table->boolean('enabled')->default(true);
            $table->text('content');
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prompt_line');
        Schema::dropIfExists('prompt_section');
        Schema::dropIfExists('prompt_version');
    }
};
