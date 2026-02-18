<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Set username = email for existing users where username is null and email exists
        DB::statement('UPDATE users SET username = email WHERE username IS NULL AND email IS NOT NULL');
    }

    public function down(): void
    {
        // Best-effort rollback: clear usernames that equal email
        DB::statement('UPDATE users SET username = NULL WHERE username = email');
    }
};
