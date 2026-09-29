<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SWF = Social Welfare Fund: member contributions + welfare payouts
        Schema::create('swf_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->string('receipt_no')->unique();
            $table->string('type')->default('contribution');
            $table->decimal('amount', 15, 2);
            $table->string('method')->default('cash');
            $table->date('transacted_at');
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['member_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('swf_entries');
    }
};
