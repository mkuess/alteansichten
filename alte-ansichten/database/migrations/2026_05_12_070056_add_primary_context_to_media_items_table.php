<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media_items', function (Blueprint $table) {
            $table->foreignId('primary_municipality_id')
                ->nullable()
                ->after('primary_place_id')
                ->constrained('municipalities')
                ->nullOnDelete();
            $table->foreignId('primary_district_id')
                ->nullable()
                ->after('primary_municipality_id')
                ->constrained('districts')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('media_items', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Municipality::class, 'primary_municipality_id');
            $table->dropForeignIdFor(\App\Models\District::class, 'primary_district_id');
            $table->dropColumn(['primary_municipality_id', 'primary_district_id']);
        });
    }
};
