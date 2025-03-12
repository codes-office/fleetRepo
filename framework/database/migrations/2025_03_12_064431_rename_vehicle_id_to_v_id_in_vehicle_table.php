<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        //thi nis to chnage
        Schema::table('vehicle', function (Blueprint $table) {
            $table->renameColumn('vehicle_id', 'v_id');
        });
    }

    public function down() {
        Schema::table('vehicle', function (Blueprint $table) {
            $table->renameColumn('v_id', 'vehicle_id');
        });
    }
};
