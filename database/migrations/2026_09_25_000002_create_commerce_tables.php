<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->string('vin', 17)->nullable();
            $table->string('part_number', 60)->nullable();
            $table->string('part_name', 150);
            $table->string('car_model', 100)->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->string('image')->nullable();
            $table->text('message')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->text('admin_reply')->nullable();
            $table->decimal('quoted_price_omr', 12, 3)->nullable();
            $table->unsignedBigInteger('quoted_price_toman')->nullable();
            $table->foreignId('replied_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('pending')->index();
            $table->unsignedInteger('items_count')->default(0);
            $table->decimal('subtotal_omr', 12, 3)->default(0);
            $table->unsignedBigInteger('total_toman')->default(0);
            $table->unsignedBigInteger('exchange_rate')->default(0);
            $table->string('full_name', 120);
            $table->string('phone', 15);
            $table->string('province', 50);
            $table->string('city', 50);
            $table->string('address', 500);
            $table->string('postal_code', 10)->nullable();
            $table->text('customer_note')->nullable();
            $table->text('admin_note')->nullable();
            $table->boolean('stock_deducted')->default(false);
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->string('part_number', 60)->nullable();
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price_omr', 12, 3);
            $table->unsignedBigInteger('unit_price_toman');
            $table->timestamps();
        });

        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->string('subtitle', 300)->nullable();
            $table->string('badge', 60)->nullable();
            $table->string('image')->nullable();
            $table->string('button_text', 40)->nullable();
            $table->string('button_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->string('key', 60)->primary();
            $table->text('value')->nullable();
        });

        Schema::create('verification_codes', function (Blueprint $table) {
            $table->id();
            $table->string('identifier', 190)->index();
            $table->string('channel', 10);   // email | phone
            $table->string('purpose', 20);   // register | reset | contact
            $table->string('code_hash', 64);
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('ip', 45)->nullable();
            $table->timestamp('expires_at');
            $table->timestamp('consumed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 60)->index();
            $table->nullableMorphs('subject');
            $table->string('description', 500)->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamp('created_at')->nullable()->index();
        });

        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->char('visitor_hash', 64);
            $table->unsignedInteger('hits')->default(1);
            $table->unique(['date', 'visitor_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('verification_codes');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('inquiries');
    }
};
