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
        Schema::table('obligation_request_items', function (Blueprint $table) {
            $table->unsignedBigInteger('account_id')->nullable()->change();
            $table->unsignedBigInteger('fund_type_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obligation_request_items', function (Blueprint $table) {
            $table->unsignedBigInteger('account_id')->nullable(false)->change();
            $table->unsignedBigInteger('fund_type_id')->nullable(false)->change();
        });
    }
};
