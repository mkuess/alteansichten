<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('primary_place_id')
                ->nullable()
                ->constrained('places')
                ->nullOnDelete();

            $table->string('type')->default('image');
            $table->string('title');
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->string('external_url')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('date_text')->nullable();
            $table->text('source_note')->nullable();
            $table->text('rights_note')->nullable();
            $table->string('rights_status')->default('needs_review');
            $table->string('location_status')->default('unknown');
            $table->string('location_note')->nullable();
            $table->string('status')->default('pending');
            $table->string('internal_reference_code', 50)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_items');
    }
};
