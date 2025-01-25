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
            $table->id();
        $table->unsignedBigInteger('tenant_id');
        $table->unsignedBigInteger('complaint_manager_id')->nullable();
        $table->text('complaint');
        $table->enum('status', ['Pending', 'In Progress', 'Resolved'])->default('Pending');
        $table->timestamps();

        $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        $table->foreign('complaint_manager_id')->references('id')->on('complaint_managers')->onDelete('set null');
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