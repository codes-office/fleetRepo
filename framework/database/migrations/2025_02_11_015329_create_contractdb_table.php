<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('contractdb', function (Blueprint $table) {
            $table->id();
            $table->string('contractType');
            $table->string('contractTypePackage')->default('PACKAGE');
            $table->string('shortCode');
            $table->integer('numberOfDuties');
            $table->integer('allottedKmPerMonth');
            $table->integer('minHoursPerDay');
            $table->decimal('packageCostPerMonth', 10, 2);
            $table->decimal('pricingForExtraDuty', 10, 2);
            $table->decimal('costPerKmAfterMinKm', 10, 2);
            $table->decimal('costPerHourAfterMinHours', 10, 2);
            $table->integer('garageKmOnReporting');
            $table->integer('garageHoursPerDay');
            $table->decimal('baseDieselPrice', 10, 2);
            $table->decimal('mileage', 10, 2);
            $table->integer('seatingCapacity');
            $table->decimal('acPriceAdjustmentPerKm', 10, 2);
            $table->integer('minTripsPerMonth');
            $table->string('Vechiletype');
            $table->string('company_name');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('contractdb');
    }
};

