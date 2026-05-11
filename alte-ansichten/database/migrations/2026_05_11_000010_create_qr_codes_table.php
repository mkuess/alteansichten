<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('place_id')
                ->unique()
                ->constrained('places')
                ->cascadeOnDelete();

            $table->string('code')->unique();
            $table->string('target_url');
            $table->string('png_path')->nullable();
            $table->string('svg_path')->nullable();
            $table->unsignedInteger('scan_count')->default(0);
            $table->timestamp('last_scanned_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_codes');
    }
};
