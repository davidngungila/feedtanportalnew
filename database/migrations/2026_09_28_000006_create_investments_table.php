<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->string('investment_no')->unique();
            $table->decimal('amount', 15, 2);
            $table->decimal('expected_return_rate', 5, 2)->default(0);
            $table->decimal('expected_return', 15, 2)->default(0);
            $table->date('start_date');
            $table->date('maturity_date')->nullable();
            $table->string('status')->default('active');
            $table->string('plan')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['member_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investments');
    }
};
