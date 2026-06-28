<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('address.tables.districts', 'mca_districts'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained(config('address.tables.cities', 'mca_cities'))->cascadeOnDelete();
            $table->string('city_code', 10)->nullable();
            $table->string('title');
            $table->string('slug')->nullable();
            $table->string('code', 20)->nullable();
            $table->unsignedBigInteger('uavt_code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['city_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('address.tables.districts', 'mca_districts'));
    }
};
