<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {            
            // Data tambahan untuk kebutuhan Karyawan/Absensi
            $table->string('role', 20)->default('karyawan')->after('email');
            $table->string('phone', 20)->nullable()->after('role');
            $table->enum('gender', ['Laki-laki', 'Perempuan'])->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'gender']);
        });
    }
};
