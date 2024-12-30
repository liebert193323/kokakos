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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();  // ID keluhan
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');  // Relasi ke tenant
            $table->text('complaint');  // Isi keluhan
            $table->enum('status', ['pending', 'in_process', 'resolved'])->default('pending');  // Status keluhan
            $table->timestamps();  // Timestamps (created_at, updated_at)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
