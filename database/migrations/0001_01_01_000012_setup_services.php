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
        Schema::create('service_departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('image');
            $table->tinyInteger('have_composit_services');
            $table->timestamps();
        });
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('service_department_id')->constrained('service_departments')->onDelete('cascade');
            $table->decimal('charges', 10, 2);
            $table->tinyInteger('charges_include_tax');
            $table->double('tax_rate');
            $table->string('slug')->index();
            $table->tinyInteger('have_service_provider')->default(0);
            $table->json('service_provider_types')->nullable();
            $table->tinyInteger('is_composit_service')->default(0);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->integer('old_id')->nullable()->index();
            $table->timestamps();
        });
        Schema::create('service_recestations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('service_department_id')->constrained('service_departments')->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('cascade');
            $table->decimal('charges', 10, 2);
            $table->tinyInteger('charges_include_tax');
            $table->double('tax_rate');
            $table->string('slug')->index();
            $table->tinyInteger('have_service_provider')->default(0);
            $table->json('service_provider_types')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->integer('old_id')->nullable()->index();
            $table->timestamps();
        });
        Schema::create('service_orders', function (Blueprint $table) {

            $table->id();
            $table->string('type')->index();
            $table->string('so_number')->unique()->index();
            $table->string('so_short')->index()->unique();

            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');

            $table->foreignId('patient_id')->nullable()->constrained('patients')->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('cascade');
            $table->foreignId('service_recestation_id')->nullable()->constrained('service_recestations')->onDelete('cascade');
            $table->foreignId('doctor_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->tinyInteger('is_composit')->default(0);

            $table->text('notes')->nullable();
            $table->json('notes_json')->nullable();

            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['patient_id']);
            $table->dropForeign(['service_id']);
            $table->dropForeign(['service_recestation_id']);
            $table->dropForeign(['doctor_id']);
        });

        Schema::table('service_recestations', function (Blueprint $table) {
            $table->dropForeign(['service_department_id']);
            $table->dropForeign(['service_id']);
            $table->dropForeign(['created_by']);
        });
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['service_department_id']);
            $table->dropForeign(['created_by']);
        });

        Schema::dropIfExists('service_orders');
        Schema::dropIfExists('service_recestations');
        Schema::dropIfExists('services');
        Schema::dropIfExists('service_departments');
    }
};
