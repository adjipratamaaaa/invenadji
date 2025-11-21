<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Cek dulu apakah kolom sudah ada, jika belum baru tambahkan
            if (!Schema::hasColumn('products', 'barcode')) {
                $table->string('barcode')->unique()->nullable()->after('id');
            }
            if (!Schema::hasColumn('products', 'barcode_type')) {
                $table->string('barcode_type')->default('C128')->after('barcode');
            }
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            // Hanya drop kolom jika ada
            if (Schema::hasColumn('products', 'barcode')) {
                $table->dropColumn('barcode');
            }
            if (Schema::hasColumn('products', 'barcode_type')) {
                $table->dropColumn('barcode_type');
            }
        });
    }
};