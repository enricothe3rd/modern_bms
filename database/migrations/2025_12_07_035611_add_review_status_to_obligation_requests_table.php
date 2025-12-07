<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('obligation_requests', function (Blueprint $table) {
            $table->foreignId('review_status_id')->nullable()->after('status')->constrained('review_statuses')->onDelete('set null');
            $table->index('review_status_id');
        });
    }

    public function down(): void
    {
        Schema::table('obligation_requests', function (Blueprint $table) {
            $table->dropForeign(['review_status_id']);
            $table->dropIndex(['review_status_id']);
            $table->dropColumn('review_status_id');
        });
    }
};
