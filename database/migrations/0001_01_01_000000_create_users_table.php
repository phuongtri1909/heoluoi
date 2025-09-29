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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->integer('coins')->default(0);
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->enum('role', ['admin_main','admin_sub','user'])->default('user');
            $table->enum('active', ['active', 'inactive'])->default('inactive');
            $table->string('key_active')->nullable();
            $table->string('key_reset_password')->nullable();
            $table->timestamp('reset_password_at')->nullable();
            $table->string('google_id')->nullable();

            $table->boolean('ban_login')->default(false);
            $table->boolean('ban_comment')->default(false);
            $table->boolean('ban_rate')->default(false);
            $table->boolean('ban_read')->default(false);

            $table->string('ip_address')->nullable();
            
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        }, 'ENGINE=InnoDB');

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
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
