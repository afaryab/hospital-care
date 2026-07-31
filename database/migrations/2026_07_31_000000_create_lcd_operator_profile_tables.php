<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'lcd_opd_operators',
            'lcd_ind_operators',
            'lcd_emergency_operators',
            'lcd_dental_operators',
            'lcd_laboratory_operators',
            'lcd_ultrasound_operators',
            'lcd_xray_operators',
        ];

        foreach ($tables as $table) {
            Schema::create($table, function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('authority')->default('operator')->comment('operator, supervisor');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('lcd_xray_operators');
        Schema::dropIfExists('lcd_ultrasound_operators');
        Schema::dropIfExists('lcd_laboratory_operators');
        Schema::dropIfExists('lcd_dental_operators');
        Schema::dropIfExists('lcd_emergency_operators');
        Schema::dropIfExists('lcd_ind_operators');
        Schema::dropIfExists('lcd_opd_operators');
    }
};
