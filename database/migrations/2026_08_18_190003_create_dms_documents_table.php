<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dms_documents', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('folder_id')->constrained('dms_folders')->restrictOnDelete();
            $table->string('name');
            $table->foreignId('classification_id')->nullable()->constrained('dms_classifications')->nullOnDelete();
            // Set for system-generated documents linked to e.g. an ExpenseVoucher.
            $table->nullableMorphs('owner');
            $table->string('status')->default('draft');
            $table->boolean('is_locked')->default(false);
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('locked_at')->nullable();
            $table->unsignedInteger('current_version')->default(1);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index('folder_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dms_documents');
    }
};
