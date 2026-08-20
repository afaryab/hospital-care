<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * Attachments were previously stored on the public disk (world-readable
     * once the URL was known). Moves any files an existing deployment
     * already uploaded over to the private disk so they match the code's
     * new expectations, without touching the file_path column values.
     */
    public function up(): void
    {
        $paths = DB::table('treatment_attachments')->pluck('file_path');

        foreach ($paths as $path) {
            if (! Storage::disk('public')->exists($path) || Storage::disk('local')->exists($path)) {
                continue;
            }

            Storage::disk('local')->put($path, Storage::disk('public')->get($path));
            Storage::disk('public')->delete($path);
        }
    }

    public function down(): void
    {
        $paths = DB::table('treatment_attachments')->pluck('file_path');

        foreach ($paths as $path) {
            if (! Storage::disk('local')->exists($path) || Storage::disk('public')->exists($path)) {
                continue;
            }

            Storage::disk('public')->put($path, Storage::disk('local')->get($path));
            Storage::disk('local')->delete($path);
        }
    }
};
