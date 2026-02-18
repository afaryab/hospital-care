<?php

use App\Models\Panel;
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
        Schema::create('panels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('panel_id')->nullable()->after('patient_id');
            $table->foreign('panel_id')->references('id')->on('panels')->onDelete('set null');
        });

        Schema::table('receaveables', function (Blueprint $table) {
            $table->unsignedBigInteger('panel_id')->nullable()->after('patient_id');
            $table->foreign('panel_id')->references('id')->on('panels')->onDelete('set null');
        });

        Panel::create([
            'name' => 'Jubilee Insurance Company Limited',
            'code' => 'JUBILEE',
            'is_active' => true,
        ]);
        // Adamjee Insurance Company Limited
        Panel::create([
            'name' => 'Adamjee Insurance Company Limited',
            'code' => 'ADAMJEE',
            'is_active' => true,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receaveables', function (Blueprint $table) {
            $table->dropForeign(['panel_id']);
            $table->dropColumn('panel_id');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['panel_id']);
            $table->dropColumn('panel_id');
        });

        Schema::dropIfExists('panels');
    }
};
