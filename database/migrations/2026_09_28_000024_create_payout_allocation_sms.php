<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('investment_payouts', function (Blueprint $table) {
            $table->json('allocation')->nullable()->after('decision_notes');
            $table->timestamp('sms_sent_at')->nullable()->after('allocation');
        });

        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investment_payout_id')->nullable()->constrained('investment_payouts')->nullOnDelete();
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->string('phone', 30);
            $table->text('message');
            $table->string('status')->default('queued');
            $table->text('provider_response')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
        Schema::table('investment_payouts', function (Blueprint $table) {
            $table->dropColumn(['allocation', 'sms_sent_at']);
        });
    }
};
