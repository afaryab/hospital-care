<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drug_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('drugs', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // Brand / common name
            $table->string('generic_name')->nullable();      // Salt / active ingredient
            $table->string('type', 50)->nullable();          // Tablet, Capsule, Syrup, Injection …
            $table->foreignId('drug_category_id')->nullable()->constrained('drug_categories')->nullOnDelete();
            $table->string('strength', 100)->nullable();     // e.g. 500mg, 250mg/5ml
            $table->string('manufacturer')->nullable();
            $table->string('default_dose', 100)->nullable();
            $table->string('default_frequency', 100)->nullable(); // TDS, BD, OD …
            $table->string('default_duration', 100)->nullable();  // 5 days, 7 days …
            $table->string('default_route', 50)->nullable();      // Oral, IV, IM, Topical …
            $table->text('usage_instructions')->nullable();
            $table->text('contraindications')->nullable();
            $table->text('side_effects')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('old_id')->nullable()->index();
            $table->timestamps();

            $table->index(['name', 'generic_name']);
            $table->index('drug_category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drugs');
        Schema::dropIfExists('drug_categories');
    }
};
