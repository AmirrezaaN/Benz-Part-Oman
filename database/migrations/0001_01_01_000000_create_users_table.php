<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();          // slug: super-admin, admin, customer ...
            $table->string('label', 100);                  // Persian display name
            $table->string('description')->nullable();
            $table->json('permissions')->nullable();       // list of permission keys from config/permissions.php
            $table->boolean('is_system')->default(false);  // system roles cannot be deleted
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            // Immutable internal numeric code — never shown to the user, never changes.
            $table->unsignedBigInteger('internal_code')->unique();
            $table->string('first_name', 60)->nullable();
            $table->string('last_name', 60)->nullable();
            $table->string('username', 30)->unique();
            $table->string('email')->nullable()->unique();
            $table->string('phone', 15)->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('password');
            $table->foreignId('role_id')->nullable()->constrained()->nullOnDelete();

            // Default shipping address (pre-fills checkout)
            $table->string('province', 50)->nullable();
            $table->string('city', 50)->nullable();
            $table->string('address', 500)->nullable();
            $table->string('postal_code', 10)->nullable();

            $table->timestamp('banned_at')->nullable();
            $table->string('ban_reason')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->rememberToken();
            $table->timestamps();
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

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
    }
};
