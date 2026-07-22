<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('triages', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('color', 30);
            $table->unsignedInteger('priority')->default(99);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('priority');
        });

        // Seed the 5 triage levels that were previously hardcoded on the
        // Service color select — configurable rows from day one.
        DB::table('triages')->insertOrIgnore([
            [
                'name' => 'Immediate Resuscitation',
                'color' => 'red',
                'priority' => 1,
                'description' => 'Life-threatening emergency. Patient must be seen immediately. Do NOT delay.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Emergency',
                'color' => 'yellow',
                'priority' => 2,
                'description' => 'High-risk. Seen within 10–15 minutes. Ongoing monitoring required.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Urgent',
                'color' => 'blue',
                'priority' => 3,
                'description' => 'Moderate risk. Seen within 30 minutes.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Semi Urgent',
                'color' => 'sky',
                'priority' => 4,
                'description' => 'Lower risk. Seen within 60 minutes.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Non Urgent',
                'color' => 'green',
                'priority' => 5,
                'description' => 'Routine. Seen within 2 hours.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('triages');
    }
};
