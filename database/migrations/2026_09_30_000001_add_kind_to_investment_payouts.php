<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('investment_payouts', function (Blueprint $table) {
            $table->string('kind', 20)->default('matured')->after('investment_id');
            $table->index('kind');
        });

        // Backfill existing rows explicitly (in case of DBs without column default).
        \Illuminate\Support\Facades\DB::table('investment_payouts')
            ->whereNull('kind')
            ->orWhere('kind', '')
            ->update(['kind' => 'matured']);
    }

    public function down(): void
    {
        Schema::table('investment_payouts', function (Blueprint $table) {
            $table->dropIndex(['kind']);
            $table->dropColumn('kind');
        });
    }
};
