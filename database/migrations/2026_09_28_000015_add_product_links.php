<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->foreignId('member_type_id')->nullable()->after('member_no')->constrained('member_types')->nullOnDelete();
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->foreignId('loan_product_id')->nullable()->after('member_id')->constrained('loan_products')->nullOnDelete();
        });

        Schema::table('investments', function (Blueprint $table) {
            $table->foreignId('investment_product_id')->nullable()->after('member_id')->constrained('investment_products')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('investment_product_id');
        });
        Schema::table('loans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('loan_product_id');
        });
        Schema::table('members', function (Blueprint $table) {
            $table->dropConstrainedForeignId('member_type_id');
        });
    }
};
