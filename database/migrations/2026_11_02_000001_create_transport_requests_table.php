<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transport_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->string('pickup_address');
            $table->string('pickup_city');
            $table->string('destination_address');
            $table->string('destination_city');
            $table->string('goods_category');
            $table->text('goods_description');
            $table->string('quantity');
            $table->unsignedInteger('weight_kg');
            $table->boolean('is_fragile')->default(false);
            $table->boolean('needs_loading_help')->default(false);
            $table->text('special_instructions')->nullable();
            $table->string('status')->index();
            $table->timestamp('submitted_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_requests');
    }
};
