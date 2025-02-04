<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Hapus foreign key lama yang mengacu ke tabel tenants
            $table->dropForeign('rooms_tenant_id_foreign');
            
            // Tambah foreign key baru yang mengacu ke tabel users
            $table->foreign('tenant_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Hapus foreign key yang mengacu ke users
            $table->dropForeign(['tenant_id']);
            
            // Kembalikan foreign key ke tenants (jika perlu rollback)
            $table->foreign('tenant_id')
                  ->references('id')
                  ->on('tenants')
                  ->onDelete('set null');
        });
    }
};