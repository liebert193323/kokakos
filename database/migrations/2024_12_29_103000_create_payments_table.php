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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('room_id')->nullable()->constrained('rooms')->onDelete('cascade'); // Ubah menjadi nullable()
            $table->foreignId('bill_id')->nullable()->constrained('bills')->onDelete('cascade');
            $table->enum('payment_category', ['semester', 'year']);
            $table->bigInteger('amount')->default(0); // Hapus default value karena akan diisi dari bill
            $table->timestamp('payment_date'); // Ubah ke timestamp untuk menyimpan waktu juga
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
