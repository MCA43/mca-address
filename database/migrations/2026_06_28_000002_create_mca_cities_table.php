<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('address.tables.cities', 'mca_cities'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained(config('address.tables.countries', 'mca_countries'))->cascadeOnDelete();
            $table->string('country_code', 10);
            $table->string('title');
            $table->string('slug')->nullable();
            $table->string('code', 10)->nullable()->comment('Plate code for TR');
            $table->unsignedBigInteger('uavt_code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['country_id', 'is_active']);
            $table->unique(['country_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('address.tables.cities', 'mca_cities'));
    }
};
