<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investment_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investment_id')->nullable()->constrained('investments')->nullOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->string('phone', 30);
            $table->decimal('amount', 15, 2);
            $table->decimal('loan_installment', 15, 2)->default(0);
            $table->decimal('swf_deduction', 15, 2)->default(0);
            $table->decimal('fines_deduction', 15, 2)->default(0);
            $table->decimal('tshirt_deduction', 15, 2)->default(0);
            $table->decimal('capital_cmg', 15, 2)->default(0);
            $table->decimal('net_cash', 15, 2);
            $table->string('verify_code', 12)->unique();
            $table->string('status')->default('pending');
            $table->string('decision')->nullable();
            $table->text('decision_notes')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['status', 'verify_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investment_payouts');
    }
};
