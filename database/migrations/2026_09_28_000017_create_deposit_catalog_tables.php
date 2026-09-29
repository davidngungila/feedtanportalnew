<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deposit_products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->decimal('interest_rate', 5, 2)->default(0);
            $table->decimal('min_amount', 15, 2)->default(100);
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('savings_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->decimal('target_amount', 15, 2)->default(0);
            $table->integer('duration_months')->default(12);
            $table->foreignId('deposit_product_id')->nullable()->constrained('deposit_products')->nullOnDelete();
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::table('deposits', function (Blueprint $table) {
            $table->foreignId('deposit_product_id')->nullable()->after('member_id')->constrained('deposit_products')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropConstrainedForeignId('deposit_product_id');
        });
        Schema::dropIfExists('savings_plans');
        Schema::dropIfExists('deposit_products');
    }
};
