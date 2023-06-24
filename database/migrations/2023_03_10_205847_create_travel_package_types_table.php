<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('travel_package_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_package_id')->constrained('travel_packages');
            $table->string('title');
            $table->float('fee');
            $table->integer('max_person');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_package_types');
    }
};
