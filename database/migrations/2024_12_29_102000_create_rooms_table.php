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
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('number')->default('No Room'); // Tambahkan default value
            $table->integer('capacity')->default(1); // Tambahkan default capacity
            $table->enum('status', ['available', 'occupied', 'archived'])->default('available');
            $table->enum('payment_category', ['semester', 'year'])->nullable();
            $table->decimal('price_per_semester', 10, 2)->default(0);
            $table->decimal('price_per_year', 10, 2)->default(0);
            $table->date('rent_start_date')->nullable();
            $table->date('rent_end_date')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rooms');
    }
};
