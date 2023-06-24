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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('agency_id')->constrained('agencies');
            $table->foreignId('travel_package_id')->constrained('travel_packages');
            $table->foreignId('travel_package_type_id')->constrained('travel_package_types');
            $table->foreignId('timeslot_id')->constrained('timeslots');
            $table->longText('message')->nullable();
            $table->boolean('reviewed')->default(false);
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
