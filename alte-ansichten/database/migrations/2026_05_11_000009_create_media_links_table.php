<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_links', function (Blueprint $table) {
            $table->id();

            $table->foreignId('media_item_id')
                ->constrained('media_items')
                ->cascadeOnDelete();

            $table->string('linkable_type');
            $table->unsignedBigInteger('linkable_id');

            $table->string('relationship_type')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedSmallInteger('period_from')->nullable();
            $table->unsignedSmallInteger('period_to')->nullable();

            $table->timestamps();

            $table->index('media_item_id');
            $table->index(['linkable_type', 'linkable_id']);
            $table->index(['media_item_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_links');
    }
};
