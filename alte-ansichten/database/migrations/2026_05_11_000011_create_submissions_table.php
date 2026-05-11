<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('place_id')
                ->nullable()
                ->constrained('places')
                ->nullOnDelete();

            $table->foreignId('municipality_id')
                ->nullable()
                ->constrained('municipalities')
                ->nullOnDelete();

            $table->string('submitted_by_name')->nullable();
            $table->string('submitted_by_email')->nullable();
            $table->string('submitted_by_phone')->nullable();

            $table->string('title');
            $table->longText('message')->nullable();

            $table->string('material_type')->nullable();
            $table->text('source_note')->nullable();

            $table->boolean('rights_confirmation')->default(false);
            $table->text('rights_note')->nullable();

            $table->string('status')->default('pending');
            $table->text('review_note')->nullable();

            $table->foreignId('reviewed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
