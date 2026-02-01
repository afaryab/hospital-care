<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('abacus_currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name')->nullable();
            $table->string('symbol')->nullable();
            $table->timestamps();
        });

        DB::table('abacus_currencies')->insert([
            'id' => 1,
            'code' => 'PKR',
            'name' => 'Pakistani Rupee',
            'symbol' => '₨',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('abacus_currencies');
    }
};
