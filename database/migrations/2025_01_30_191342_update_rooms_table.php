<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Hapus foreign key jika sudah ada sebelumnya
            $table->dropForeign(['user_id']);

            // Mengubah user_id agar bisa diupdate dan bisa NULL
            $table->unsignedBigInteger('user_id')->nullable()->change();

            // Tambahkan foreign key lagi dengan ON DELETE SET NULL
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            // Pastikan rent_start_date dan rent_end_date bisa NULL
            $table->date('rent_start_date')->nullable()->change();
            $table->date('rent_end_date')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Hapus foreign key sebelum rollback
            $table->dropForeign(['user_id']);

            // Kembalikan user_id menjadi tidak nullable (jika sebelumnya tidak nullable)
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }
};
