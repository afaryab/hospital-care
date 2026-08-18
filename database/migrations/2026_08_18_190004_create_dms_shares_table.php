<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dms_shares', function (Blueprint $table) {
            $table->id();
            // Exactly one of document_id / folder_id is set — enforced in DmsShare, not the DB.
            $table->foreignId('document_id')->nullable()->constrained('dms_documents')->cascadeOnDelete();
            $table->foreignId('folder_id')->nullable()->constrained('dms_folders')->cascadeOnDelete();
            $table->string('grantee_type');
            $table->string('grantee_value');
            $table->string('ability')->default('view');
            $table->string('token')->nullable()->unique();
            $table->timestamp('expires_at')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('accessed_at')->nullable();
            $table->timestamps();

            $table->index(['grantee_type', 'grantee_value']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dms_shares');
    }
};
