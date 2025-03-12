<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('Driver_name');
            $table->string('city');
            $table->date('DOB');
            $table->string('phone_code');
            $table->string('phone');
            $table->string('api_token');
            $table->string('emp_id')->unique();
            $table->unsignedBigInteger('vendor_id');
            $table->string('license_number');
            $table->date('license_number_date');
            $table->date('induction_date');
            $table->string('badge_number');
            $table->date('badge_number_date');
            $table->string('alternate_gov_id');
            $table->string('alternate_gov_id_number');
            $table->string('background_verification_status');
            $table->date('background_verification_date');
            $table->string('police_verification_status');
            $table->date('police_verification_date');
            $table->string('medical_verification_status');
            $table->date('medical_verification_date');
            $table->date('training_date');
            $table->date('eye_test_date');
            $table->string('driver_license_image')->nullable();
            $table->string('induction_file')->nullable();
            $table->string('alternate_gov_file')->nullable();
            $table->string('background_verification_file')->nullable();
            $table->string('police_verification_file')->nullable();
            $table->string('medical_verification_file')->nullable();
            $table->string('training_file')->nullable();
            $table->string('eye_test_file')->nullable();
            $table->string('documents_file')->nullable();
            $table->string('current_address_file')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('drivers');
    }
};
