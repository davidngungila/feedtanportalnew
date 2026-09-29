<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member_applications', function (Blueprint $table) {
            // Personal (form: sex, marital status, date of birth).
            $table->string('sex', 20)->nullable()->after('national_id');
            $table->string('marital_status', 30)->nullable()->after('sex');
            $table->date('dob')->nullable()->after('marital_status');
            // Work.
            $table->string('job', 255)->nullable()->after('address');
            $table->string('employer', 255)->nullable()->after('job');
            $table->string('statement_channel', 20)->nullable()->after('employer');
            // Bank.
            $table->string('bank_name', 255)->nullable()->after('statement_channel');
            $table->string('bank_account', 100)->nullable()->after('bank_name');
            // Membership extras.
            $table->text('biography')->nullable()->after('notes');
            $table->string('referrer', 255)->nullable()->after('biography');
            $table->boolean('consider_ordinary')->default(false)->after('referrer');
            // Group applicants.
            $table->string('group_name', 255)->nullable()->after('member_group_id');
            $table->boolean('group_registered')->default(false)->after('group_name');
            $table->string('group_leaders', 500)->nullable()->after('group_registered');
            $table->string('group_bank_account', 255)->nullable()->after('group_leaders');
            $table->string('group_contacts', 500)->nullable()->after('group_bank_account');
            // Savings goal.
            $table->string('savings_goal', 255)->nullable()->after('group_contacts');
            $table->decimal('goal_amount', 15, 2)->nullable()->after('savings_goal');
            $table->unsignedInteger('goal_months')->nullable()->after('goal_amount');
            $table->date('goal_start')->nullable()->after('goal_months');
            // Structured answers.
            $table->json('contributions')->nullable()->after('goal_start');
            $table->json('beneficiaries')->nullable()->after('contributions');
            $table->json('attachments')->nullable()->after('beneficiaries');
        });
    }

    public function down(): void
    {
        Schema::table('member_applications', function (Blueprint $table) {
            $table->dropColumn([
                'sex', 'marital_status', 'dob', 'job', 'employer', 'statement_channel',
                'bank_name', 'bank_account', 'biography', 'referrer', 'consider_ordinary',
                'group_name', 'group_registered', 'group_leaders', 'group_bank_account', 'group_contacts',
                'savings_goal', 'goal_amount', 'goal_months', 'goal_start',
                'contributions', 'beneficiaries', 'attachments',
            ]);
        });
    }
};
