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
        Schema::table('death_certificates', function (Blueprint $table) {
            $table->string('verification_token', 40)->nullable()->unique()->after('certificate_number');
        });

        Schema::table('birth_certificates', function (Blueprint $table) {
            $table->string('verification_token', 40)->nullable()->unique()->after('birth_certificate_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('death_certificates', function (Blueprint $table) {
            $table->dropColumn('verification_token');
        });

        Schema::table('birth_certificates', function (Blueprint $table) {
            $table->dropColumn('verification_token');
        });
    }
};
