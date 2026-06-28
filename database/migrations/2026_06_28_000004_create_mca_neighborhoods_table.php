<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('address.tables.neighborhoods', 'mca_neighborhoods'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained(config('address.tables.districts', 'mca_districts'))->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->unsignedBigInteger('uavt_code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['district_id', 'is_active']);
            $table->index('postal_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('address.tables.neighborhoods', 'mca_neighborhoods'));
    }
};
