<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transport_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained()->cascadeOnDelete();
            $table->string('response_status')->index();
            $table->timestamp('sent_at');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->unique(['transport_request_id', 'driver_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_invitations');
    }
};
