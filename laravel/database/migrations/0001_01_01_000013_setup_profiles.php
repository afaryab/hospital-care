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
        Schema::create('administrators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('authority')->default('assistant')->comment('executive, administrator, superadmin');
            $table->timestamps();
        });

        Schema::create('accountants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('authority')->default('assistant')->comment('assistant, manager');
            $table->timestamps();
        });

        Schema::create('receptionists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('authority')->default('assistant')->comment('assistant, manager');
            $table->timestamps();
        });

        Schema::create('opd_doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('authority')->default('assistant')->comment('assistant, manager');
            $table->timestamps();
        });

        Schema::create('ind_doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('authority')->default('assistant')->comment('assistant, manager');
            $table->timestamps();
        });

        Schema::create('emergency_doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('authority')->default('assistant')->comment('assistant, manager');
            $table->timestamps();
        });

        Schema::create('dentists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('authority')->default('assistant')->comment('assistant, manager');
            $table->timestamps();
        });

        Schema::create('ultrasound_doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('authority')->default('assistant')->comment('assistant, manager');
            $table->timestamps();
        });

        Schema::create('xray_technicians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('nursing_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('authority')->default('assistant')->comment('assistant, manager');
            $table->timestamps();
        });

        Schema::create('patient_managers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        foreach([
            'administrators',
            'accountants',
            'receptionists',
            'opd_doctors',
            'ind_doctors',
            'emergency_doctors',
            'dentists',
            'ultrasound_doctors',
            'xray_technicians',
            'nursing_staff',
            'patient_managers'
        ] as $tableName){

            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
            Schema::dropIfExists($tableName);

        }
    }
};
