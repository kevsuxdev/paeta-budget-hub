<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            $table->timestamp('dept_head_reviewed_at')->nullable()->after('date_updated');
            $table->timestamp('finance_reviewed_at')->nullable()->after('dept_head_reviewed_at');
            $table->timestamp('final_approved_at')->nullable()->after('finance_reviewed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            $table->dropColumn(['dept_head_reviewed_at', 'finance_reviewed_at', 'final_approved_at']);
        });
    }
};
