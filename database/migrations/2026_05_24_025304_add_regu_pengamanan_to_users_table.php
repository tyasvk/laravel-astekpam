<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan enum dengan pilihan rupam I - IV
            $table->enum('regu_pengamanan', ['Rupam I', 'Rupam II', 'Rupam III', 'Rupam IV'])->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('regu_pengamanan');
        });
    }
};