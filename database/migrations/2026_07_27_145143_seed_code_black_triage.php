<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Standard mass-casualty triage color: Black = deceased/expectant.
        // Selecting it prompts the doctor to confirm a time of death rather
        // than representing a "how urgently to see them" priority, so it
        // sorts after the wait-time-priority colors.
        DB::table('triages')->insertOrIgnore([
            'name' => 'Code Black',
            'color' => 'black',
            'priority' => 6,
            'description' => 'Patient has been declared dead.',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('triages')->where('name', 'Code Black')->where('color', 'black')->delete();
    }
};
