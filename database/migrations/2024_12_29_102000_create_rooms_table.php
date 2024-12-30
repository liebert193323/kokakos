<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');  // Keep this for backward compatibility
            $table->string('number')->nullable();  // Add this for the new room number
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('capacity')->default(1);
            $table->enum('status', ['available', 'occupied'])->default('available');
            $table->enum('payment_category', ['semester', 'year'])->default('semester');
            $table->decimal('price_per_semester', 12, 2)->default(0);
            $table->decimal('price_per_year', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rooms');
    }
};