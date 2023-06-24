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
        Schema::create('travel_packages', function (Blueprint $table) {
            $table->id()->cascadeOnDelete();
            $table->foreignId('agency_id')->constrained('agencies');
            $table->string('title');
            $table->longText('description');
            $table->boolean('featured')->default(false);
            $table->boolean('blog')->default(false);
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_packages');
    }
};
