<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Wards — logical groupings of rooms (e.g. "Male Medical Ward", "Surgical Ward")
        Schema::create('wards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('general')->comment('general|surgical|icu|maternity|pediatric|isolation|other');
            $table->string('floor')->nullable()->comment('Ground, 1st, 2nd etc.');
            $table->string('building')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Rooms — physical rooms within a ward
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ward_id')->constrained('wards')->cascadeOnDelete();
            $table->string('name');
            $table->string('room_number')->nullable();
            $table->string('type')->default('general')->comment('general|private|semi-private|icu|isolation');
            $table->unsignedSmallInteger('capacity')->default(1)->comment('Max beds in this room');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('ward_id');
        });

        // Beds — individual beds within a room
        Schema::create('beds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->foreignId('ward_id')->constrained('wards')->cascadeOnDelete();
            $table->string('bed_number');
            $table->string('status')->default('available')->comment('available|occupied|reserved|maintenance|cleaning');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['room_id', 'bed_number']);
            $table->index(['ward_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beds');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('wards');
    }
};
