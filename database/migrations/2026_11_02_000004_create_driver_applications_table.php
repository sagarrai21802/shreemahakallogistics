<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_applications', function (Blueprint $table) {
            $table->id();
            $table->string('driver_name');
            $table->string('partner_ref')->unique();
            $table->string('vehicle_type');
            $table->unsignedTinyInteger('documents_received');
            $table->unsignedTinyInteger('documents_total');
            $table->string('status')->index();
            $table->timestamp('submitted_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_applications');
    }
};
