<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('vehicle', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->string('vehicle_id')->unique();
            $table->string('registration_no')->unique();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('inactive_reason')->nullable();
            $table->string('sim_number')->nullable();
            $table->string('device_imei')->nullable();
            $table->unsignedBigInteger('vehicle_type');
            $table->string('contract')->default('NA');
            $table->integer('working_time')->default(1440);
            $table->string('change_contract_from')->default('N/A');
            $table->string('start_hour');
            $table->string('start_minute');
            $table->string('send_audit_sms')->nullable();
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->string('mobile_number');
            $table->string('alternative_number')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down() {
        Schema::dropIfExists('vehicle');
    }
};
