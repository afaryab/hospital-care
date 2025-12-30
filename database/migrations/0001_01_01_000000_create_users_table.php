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

        Schema::create('images', function(Blueprint $table) {
            $table->id();
            $table->string('linked_to_model');
            $table->integer('linked_to_id');
            $table->string('path');
            $table->string('file_type')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->dateTime('password_expired_at')->nullable();

            $table->dateTime('last_login')->nullable();
            $table->dateTime('last_activity')->nullable();
            $table->dateTime('last_login_attempt')->nullable();

            $table->ipAddress('ip_address')->nullable();
            $table->integer('login_attempts')->default(0);

            $table->string('profile_img_path')->nullable();
            $table->foreignId('profile_img_id')->nullable()->constrained('images');

            $table->boolean('is_active')->default(true);
            $table->string('banned_message')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });

        Schema::table('images', function(Blueprint $table) {
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('uploader_id')->constrained('users')->onDelete('cascade');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->dropForeign(['uploader_id']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['profile_img_id']);
        });

        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('images');
    }
};
