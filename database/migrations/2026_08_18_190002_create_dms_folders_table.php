<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dms_folders', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('name');
            $table->foreignId('parent_id')->nullable()->constrained('dms_folders')->nullOnDelete();
            // Materialized ancestor-id path, e.g. "/1/4/9/" — used for descendant
            // lookups (LIKE prefix) and move-cycle checks without a recursive CTE.
            $table->text('path');
            $table->foreignId('classification_id')->nullable()->constrained('dms_classifications')->nullOnDelete();
            // Set for system-generated folders linked to a Patient or a doctor User.
            $table->nullableMorphs('owner');
            $table->boolean('is_system')->default(false);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dms_folders');
    }
};
